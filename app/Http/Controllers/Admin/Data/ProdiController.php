<?php

namespace App\Http\Controllers\Admin\Data;

use App\DataTables\Admin\Data\ProdiDataTable;
use App\Http\Controllers\Controller;
use App\Models\Prodi;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProdiController extends Controller
{
    public function index(ProdiDataTable $dataTable)
    {
        return $dataTable->render('admin.data.prodi');
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'kode_prodi' => 'required|string|max:255',
            'nama_prodi' => 'required|string|max:255',
            'jenjang' => 'required|string|max:255',
        ]);

        $kodeProdi = strtoupper(trim($request->kode_prodi));
        $prodi = Prodi::withTrashed()->where('kode_prodi', $kodeProdi)->first();

        if ($prodi && !$prodi->trashed()) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Kode prodi sudah terdaftar.');
        }

        if ($prodi && $prodi->trashed()) {
            $prodi->restore();
        } else {
            $prodi = new Prodi();
            $prodi->prodi_id = Str::uuid();
        }

        $prodi->kode_prodi = $kodeProdi;
        $prodi->nama_prodi = $request->nama_prodi;
        $prodi->jenjang = $request->jenjang;
        $prodi->save();
        return redirect()->route('admin.data.prodi')->with('success', 'Prodi berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $prodi = Prodi::find($request->prodi_id);
        $prodi->kode_prodi = $request->kode_prodi;
        $prodi->nama_prodi = $request->nama_prodi;
        $prodi->jenjang = $request->jenjang;
        $prodi->save();
        return redirect()->route('admin.data.prodi')->with('success', 'Prodi berhasil diubah');
    }

    public function hapus(Request $request)
    {
        $prodi = Prodi::find($request->prodi_id);
        $prodi->delete();
        return redirect()->route('admin.data.prodi')->with('success', 'Prodi berhasil dihapus');
    }
}
