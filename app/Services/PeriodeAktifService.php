<?php

namespace App\Services;

use App\Models\Penugasan;
use App\Models\Periode;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PeriodeAktifService
{
    // Memastikan periode tahun berjalan tersedia sebagai default.
    // Jika admin sudah mengaktifkan periode lain, pilihan admin tetap dihormati.
    public function sinkronkanTahunBerjalan(): ?Periode
    {
        try {
            if (!Schema::hasTable('periode')) {
                return null;
            }

            return DB::transaction(function () {
                $tahunBerjalan = (int) Carbon::now()->year;
                $periodeTahunBerjalan = $this->ambilAtauBuatPeriode($tahunBerjalan);
                $periodeAktif = Periode::where('status', '1')
                    ->orderByDesc('updated_at')
                    ->first();

                if ($periodeAktif) {
                    $this->pastikanHanyaSatuAktif($periodeAktif);
                    return $periodeAktif->fresh();
                }

                Periode::where('id', '!=', $periodeTahunBerjalan->id)
                    ->update(['status' => '0']);

                if ($periodeTahunBerjalan->status !== '1') {
                    $periodeTahunBerjalan->status = '1';
                    $periodeTahunBerjalan->save();
                }

                $this->aktifkanKembaliPenugasan($periodeTahunBerjalan);

                return $periodeTahunBerjalan->fresh();
            });
        } catch (\Throwable $exception) {
            report($exception);
            return null;
        }
    }

    // Mengambil periode berdasarkan tahun. Jika belum ada, sistem membuat data baru.
    private function ambilAtauBuatPeriode(int $tahun): Periode
    {
        $periode = Periode::withTrashed()
            ->where('tahun', $tahun)
            ->orderByRaw('deleted_at IS NULL DESC')
            ->first();

        if ($periode) {
            if ($periode->trashed()) {
                $periode->restore();
            }

            return $periode;
        }

        $periode = new Periode();
        $periode->tahun = $tahun;
        $periode->status = '0';
        $periode->save();

        return $periode;
    }

    // Jika periode tahun berjalan pernah dinonaktifkan, penugasannya dikembalikan aktif.
    private function aktifkanKembaliPenugasan(Periode $periode): void
    {
        Penugasan::where('periode_id', $periode->id)
            ->where('status_penugasan', 'selesai')
            ->update(['status_penugasan' => 'aktif']);
    }

    // Menjaga agar tetap hanya satu periode yang aktif.
    private function pastikanHanyaSatuAktif(Periode $periodeAktif): void
    {
        Periode::where('status', '1')
            ->where('id', '!=', $periodeAktif->id)
            ->update(['status' => '0']);
    }
}
