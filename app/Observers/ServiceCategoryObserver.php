<?php

namespace App\Observers;

use App\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

class ServiceCategoryObserver extends LoggedModelObserver
{
    protected function menu(): string
    {
        return 'Kategori';
    }

    protected function noun(): string
    {
        return 'kategori';
    }

    protected function labels(): array
    {
        return [
            'name' => 'Nama', 'slug' => 'Alamat URL', 'description' => 'Deskripsi', 'image_path' => 'Gambar',
            'icon' => 'Ikon', 'icon_image_path' => 'Ikon kustom', 'is_published' => 'Status',
        ];
    }

    protected function details(Model $model, string $event): ?string
    {
        return $event === 'created' ? 'status: '.ActivityLogger::status($model->is_published) : null;
    }
}
