<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use App\Models\BusinessActivity;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelObserverLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_changes_without_a_logged_in_admin_are_not_recorded(): void
    {
        $category = ServiceCategory::factory()->create();
        $category->update(['name' => 'Nama Baru']);
        $category->delete();

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_create_update_and_delete_by_an_admin_are_recorded_by_the_observer(): void
    {
        $this->actingAs(User::factory()->create(['name' => 'Admin Uji']));

        $category = ServiceCategory::factory()->create(['name' => 'Kategori A', 'is_published' => true]);
        $created = ActivityLog::where('action', 'created')->sole();
        $this->assertSame('Kategori', $created->menu);
        $this->assertSame('Admin Uji', $created->user_name);
        $this->assertSame("category:{$category->id}", $created->subject);
        $this->assertSame('Menambahkan kategori "Kategori A" (status: Terbit)', $created->detail);

        $category->update(['name' => 'Kategori B']);
        $this->assertSame(
            'Mengubah kategori "Kategori B" (yang diubah: Nama)',
            ActivityLog::where('action', 'updated')->sole()->detail,
        );

        $category->delete();
        $deleted = ActivityLog::where('action', 'deleted')->sole();
        $this->assertSame('Menghapus kategori "Kategori B"', $deleted->detail);
        $this->assertNull($deleted->subject);
    }

    public function test_an_update_that_changes_no_logged_column_is_not_recorded(): void
    {
        $this->actingAs(User::factory()->create());
        $category = ServiceCategory::factory()->create();
        ActivityLog::query()->delete();

        $category->update(['sort_order' => 99]);

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_bulk_query_updates_do_not_trigger_the_observer(): void
    {
        $this->actingAs(User::factory()->create());
        $category = ServiceCategory::factory()->create();
        ActivityLog::query()->delete();

        ServiceCategory::whereKey($category->id)->update(['name' => 'Lewat Query']);

        $this->assertSame(0, ActivityLog::count());
    }

    public function test_the_activity_observer_names_the_category(): void
    {
        $this->actingAs(User::factory()->create());
        $category = ServiceCategory::factory()->create(['name' => 'Induk']);

        $activity = BusinessActivity::factory()->for($category, 'category')->create(['name' => 'Akt', 'is_published' => false]);
        $this->assertSame(
            'Menambahkan aktivitas "Akt" (kategori: Induk, status: Draft)',
            ActivityLog::where('menu', 'Aktivitas Usaha')->where('action', 'created')->sole()->detail,
        );

        $activity->delete();
        $this->assertSame(
            'Menghapus aktivitas "Akt" (kategori: Induk)',
            ActivityLog::where('menu', 'Aktivitas Usaha')->where('action', 'deleted')->sole()->detail,
        );
    }
}
