<?php

namespace App\Observers;

use App\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class BusinessActivityObserver extends LoggedModelObserver
{
    protected function menu(): string
    {
        return 'Aktivitas Usaha';
    }

    protected function noun(): string
    {
        return 'aktivitas';
    }

    protected function labels(): array
    {
        return [
            'service_category_id' => 'Kategori', 'kbli_code' => 'Kode KBLI', 'name' => 'Nama', 'slug' => 'Alamat URL',
            'short_description' => 'Deskripsi singkat', 'description' => 'Deskripsi detail', 'image_path' => 'Gambar',
            'is_published' => 'Status',
        ];
    }

    protected function details(Model $model, string $event): ?string
    {
        $category = $model->category()->value('name');

        return $event === 'created'
            ? "kategori: {$category}, status: ".ActivityLogger::status($model->is_published)
            : "kategori: {$category}";
    }
}
