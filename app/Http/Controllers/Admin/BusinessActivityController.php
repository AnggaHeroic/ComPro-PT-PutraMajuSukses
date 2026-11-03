<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BusinessActivityRequest;
use App\Http\Requests\Admin\ReorderItemsRequest;
use App\Models\ActivityLog;
use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use App\Services\ImageUploader;
use App\Support\ActivityLogger;
use App\Support\Reorder;
use App\Support\SaveRedirect;
use App\Support\StatusFilter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BusinessActivityController extends Controller
{
    private const NON_COLUMN_FIELDS = ['image', 'remove_image', 'status'];

    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q'));
        $categoryId = $request->integer('category') ?: null;
        $status = StatusFilter::fromRequest($request);
        $counts = StatusFilter::counts(
            BusinessActivity::query()->when($categoryId, fn ($query) => $query->where('service_category_id', $categoryId))
        );

        // Mode atur urutan: tanpa pencarian/filter status; semua baris dimuat agar urutan per kategori utuh.
        $reorder = $request->boolean('reorder') && $search === '' && $status === null;

        $query = BusinessActivity::query()
            ->tap(fn ($query) => StatusFilter::apply($query, $status))
            ->with('category:id,name,slug')
            ->withCount('images')
            ->when($categoryId, fn ($query) => $query->where('service_category_id', $categoryId))
            ->when($search !== '', fn ($query) => $query->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('kbli_code', 'like', "%{$search}%");
            }))
            ->orderBy('service_category_id')
            ->ordered();

        $activities = $reorder ? $query->get() : $query->paginate(15)->withQueryString();

        return view('admin.activities.index', [
            'activities' => $activities,
            'categories' => ServiceCategory::ordered()->get(['id', 'name']),
            'search' => $search,
            'categoryId' => $categoryId,
            'status' => $status,
            'counts' => $counts,
            'reorder' => $reorder,
        ]);
    }

    public function create(Request $request): View
    {
        $categoryId = $request->integer('category') ?: null;

        $activity = new BusinessActivity([
            'service_category_id' => $categoryId,
            'sort_order' => $categoryId
                ? (int) BusinessActivity::where('service_category_id', $categoryId)->max('sort_order') + 1
                : 1,
            'is_published' => true,
        ]);

        return view('admin.activities.form', [
            'activity' => $activity,
            'categories' => ServiceCategory::ordered()->get(['id', 'name']),
        ]);
    }

    public function store(BusinessActivityRequest $request, ImageUploader $images): RedirectResponse
    {
        $path = $images->storeFromRequest($request, 'activities');

        try {
            $activity = BusinessActivity::create([
                ...$request->safe()->except(self::NON_COLUMN_FIELDS),
                'sort_order' => (int) BusinessActivity::where('service_category_id', $request->integer('service_category_id'))->max('sort_order') + 1,
                'image_path' => $path,
            ]);
        } catch (Throwable $e) {
            $images->release($path);

            throw $e;
        }

        return SaveRedirect::toIndex('admin.activities.index', $activity->is_published, 'Aktivitas berhasil ditambahkan.', ['category' => $activity->service_category_id]);
    }

    public function edit(BusinessActivity $activity): View
    {
        return view('admin.activities.form', [
            'activity' => $activity,
            'categories' => ServiceCategory::ordered()->get(['id', 'name']),
        ]);
    }

    public function update(BusinessActivityRequest $request, BusinessActivity $activity, ImageUploader $images): RedirectResponse
    {
        $data = $request->safe()->except(self::NON_COLUMN_FIELDS);
        $oldPath = $activity->image_path;
        $newPath = $images->storeFromRequest($request, 'activities');

        if ((int) $data['service_category_id'] !== $activity->service_category_id) {
            $data['sort_order'] = (int) BusinessActivity::where('service_category_id', $data['service_category_id'])->max('sort_order') + 1;
        }

        if ($newPath !== null) {
            $data['image_path'] = $newPath;
        } elseif ($request->boolean('remove_image')) {
            $data['image_path'] = null;
        }

        try {
            $activity->update($data);
        } catch (Throwable $e) {
            $images->release($newPath);

            throw $e;
        }

        if (array_key_exists('image_path', $data)) {
            $images->release($oldPath);
        }

        return SaveRedirect::toIndex('admin.activities.index', $activity->is_published, 'Aktivitas berhasil diperbarui.', ['category' => $activity->service_category_id]);
    }

    public function destroy(BusinessActivity $activity, ImageUploader $images): RedirectResponse
    {
        // Baris galeri ikut terhapus oleh database (cascade); file fisiknya dibersihkan di sini.
        $paths = $activity->images()->pluck('image_path')->push($activity->image_path)->all();
        $categoryId = $activity->service_category_id;

        $activity->delete();

        foreach ($paths as $path) {
            $images->release($path);
        }

        return redirect()
            ->route('admin.activities.index', ['category' => $categoryId])
            ->with('success', 'Aktivitas berhasil dihapus.');
    }

    /**
     * Simpan urutan baru dari drag-and-drop untuk satu kategori.
     */
    public function reorder(ReorderItemsRequest $request): JsonResponse
    {
        $categoryId = $request->integer('category');
        $order = array_map('intval', $request->validated('order'));

        if (! ServiceCategory::whereKey($categoryId)->exists()) {
            throw ValidationException::withMessages(['category' => 'Kategori tidak ditemukan.']);
        }

        Reorder::apply(BusinessActivity::where('service_category_id', $categoryId), $order, 'aktivitas');

        ActivityLogger::record(ActivityLog::ACTION_REORDERED, 'Aktivitas Usaha', 'Mengubah urutan aktivitas pada kategori "'.ServiceCategory::whereKey($categoryId)->value('name').'"');

        return response()->json(['message' => 'Urutan aktivitas disimpan.']);
    }
}
