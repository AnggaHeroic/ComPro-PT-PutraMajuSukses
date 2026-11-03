<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Menyimpan urutan baru hasil seret-dan-lepas. Daftar id yang dikirim harus persis sama dengan isi
 * lingkup yang diurutkan; bila berbeda (data berubah di tab lain), admin diminta memuat ulang.
 */
class Reorder
{
    /**
     * @param  Builder|Relation  $scope  semua baris yang diurutkan bersama (mis. satu kategori)
     * @param  array<int, int|string>  $order  id sesuai urutan baru
     * @param  string  $noun  sebutan item untuk pesan galat, mis. "kategori"
     */
    public static function apply(Builder|Relation $scope, array $order, string $noun): void
    {
        $order = array_map('intval', $order);

        $expected = (clone $scope)->pluck('id')->map(fn ($id) => (int) $id)->sort()->values()->all();

        if ($expected !== collect($order)->sort()->values()->all()) {
            throw ValidationException::withMessages([
                'order' => "Daftar {$noun} sudah berubah. Muat ulang halaman lalu coba lagi.",
            ]);
        }

        DB::transaction(function () use ($scope, $order) {
            foreach ($order as $position => $id) {
                (clone $scope)->whereKey($id)->update(['sort_order' => $position + 1]);
            }
        });
    }
}
