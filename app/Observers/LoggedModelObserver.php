<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Support\ActivityLogger;
use Illuminate\Database\Eloquent\Model;

/**
 * Dasar observer yang mencatat tambah, ubah, dan hapus sebuah model ke riwayat aktivitas.
 * Model baru cukup membuat turunan yang mengisi menu(), noun(), labels(), dan (bila perlu) details().
 *
 * Hanya perubahan yang dilakukan admin yang masuk ke riwayat; seeder, tinker, dan tes
 * (tanpa pengguna yang login) tidak dicatat. Perubahan lewat query massal seperti
 * ->update() pada builder tidak memicu observer, jadi urutan ulang dicatat manual.
 */
abstract class LoggedModelObserver
{
    /** Nama menu di riwayat aktivitas, mis. "Kategori". */
    abstract protected function menu(): string;

    /** Sebutan item dalam kalimat, mis. "kategori". */
    abstract protected function noun(): string;

    /**
     * Kolom => label yang ditampilkan pada keterangan "yang diubah".
     *
     * @return array<string, string>
     */
    abstract protected function labels(): array;

    /** Keterangan tambahan dalam kurung untuk event "created" atau "deleted"; null bila tidak ada. */
    protected function details(Model $model, string $event): ?string
    {
        return null;
    }

    public function created(Model $model): void
    {
        $this->log(ActivityLog::ACTION_CREATED, 'Menambahkan', $model, $this->details($model, 'created'), subject: $model);
    }

    public function updated(Model $model): void
    {
        $changed = ActivityLogger::changedLabels($model, $this->labels());

        if ($changed !== '') {
            $this->log(ActivityLog::ACTION_UPDATED, 'Mengubah', $model, "yang diubah: {$changed}", subject: $model);
        }
    }

    public function deleted(Model $model): void
    {
        $this->log(ActivityLog::ACTION_DELETED, 'Menghapus', $model, $this->details($model, 'deleted'));
    }

    private function log(string $action, string $verb, Model $model, ?string $details, ?Model $subject = null): void
    {
        if (! auth()->check()) {
            return;
        }

        $text = "{$verb} {$this->noun()} \"{$model->name}\"".($details === null ? '' : " ({$details})");

        ActivityLogger::record($action, $this->menu(), $text, subject: $subject);
    }
}
