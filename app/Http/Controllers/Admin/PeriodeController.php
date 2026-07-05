<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\PeriodeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Penugasan;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeController extends Controller
{
    public function index(PeriodeDataTable $dataTable)
    {
        $this->pastikanHanyaSatuAktif();

        $periode = Periode::all();
        return $dataTable->render('admin.periode', compact('periode'));
    }

    public function tambah(Request $request)
    {
        $validated = $request->validate([
            'tahun' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
            ],
        ], [
            'tahun.required' => 'Tahun periode wajib dipilih.',
            'tahun.integer' => 'Tahun periode harus berupa angka.',
        ]);

        $tahunSudahAda = Periode::where('tahun', $validated['tahun'])->exists();

        if ($tahunSudahAda) {
            return redirect()->back()->withInput()->with('error', 'Tahun sudah ada.');
        }

        DB::transaction(function () use ($validated) {
            $this->selesaikanPenugasanPeriodeAktif();

            Periode::query()->update(['status' => '0']);

            $periode = new Periode();
            $periode->tahun = $validated['tahun'];
            $periode->status = '1';
            $periode->save();
        });

        return redirect()->back()->with('success', 'Periode berhasil ditambahkan dan diaktifkan.');
    }

    public function edit(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:periode,id',
            'tahun' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
            ],
        ], [
            'tahun.required' => 'Tahun periode wajib diisi.',
            'tahun.integer' => 'Tahun periode harus berupa angka.',
        ]);

        $tahunSudahAda = Periode::where('tahun', $validated['tahun'])
            ->where('id', '!=', $validated['periode_id'])
            ->exists();

        if ($tahunSudahAda) {
            return redirect()->back()->withInput()->with('error', 'Tahun sudah ada.');
        }

        $periode = Periode::findOrFail($validated['periode_id']);
        $periode->tahun = $validated['tahun'];
        $periode->save();

        return redirect()->back()->with('success', 'Periode berhasil diperbarui.');
    }

    public function aktivasi(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:periode,id',
        ]);

        $periode = Periode::findOrFail($validated['periode_id']);

        if ($periode->status === '1') {
            $periode->status = '0';
            $periode->save();

            return redirect()->back()->with('success', 'Periode berhasil dinonaktifkan.');
        }

        DB::transaction(function () use ($periode) {
            $this->selesaikanPenugasanPeriodeAktif();

            Periode::where('id', '!=', $periode->id)->update(['status' => '0']);

            $periode->status = '1';
            $periode->save();

            $this->aktifkanPenugasanPeriode($periode);
        });

        return redirect()->back()->with('success', 'Periode berhasil diaktifkan. Periode lain otomatis dinonaktifkan.');
    }

    public function hapus(Request $request)
    {
        $validated = $request->validate([
            'periode_id' => 'required|exists:periode,id',
        ]);

        $periode = Periode::findOrFail($validated['periode_id']);
        $periode->delete();

        return redirect()->back()->with('success', 'Periode berhasil dihapus.');
    }

    private function selesaikanPenugasanPeriodeAktif(): void
    {
        $periodeAktif = Periode::where('status', '1')->first();

        if (!$periodeAktif) {
            return;
        }

        Penugasan::where('periode_id', $periodeAktif->id)
            ->update(['status_penugasan' => 'selesai']);
    }

    private function aktifkanPenugasanPeriode(Periode $periode): void
    {
        Penugasan::where('periode_id', $periode->id)
            ->where('status_penugasan', 'selesai')
            ->update(['status_penugasan' => 'aktif']);
    }

    private function pastikanHanyaSatuAktif(): void
    {
        $periodeAktif = Periode::where('status', '1')
            ->orderByDesc('updated_at')
            ->get();

        if ($periodeAktif->count() <= 1) {
            return;
        }

        Periode::where('status', '1')
            ->where('id', '!=', $periodeAktif->first()->id)
            ->update(['status' => '0']);
    }
}
