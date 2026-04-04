<?php

namespace App\Http\Controllers\Admin\Data;

use App\DataTables\Admin\Data\UPTDataTable;
use App\Http\Controllers\Controller;
use App\Models\Prodi;
use App\Models\UPT;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UPTController extends Controller
{
    public function index(UPTDataTable $dataTable)
    {
        $prodi = Prodi::all();
        return $dataTable->render('admin.data.upt', compact('prodi'));
    }

    public function tambah(Request $request)
    {
        $request->validate([
            'kategori_upt' => 'required|in:Prodi,Unit/Bagian',
            'prodi_id' => 'nullable',
            'nama_upt' => 'nullable|string|max:255',
            'kode_upt' => 'nullable|string|max:255',
        ]);

        if ($request->kategori_upt == 'Prodi') {
            $request->validate([
                'prodi_id' => 'required|exists:prodi,prodi_id',
            ]);

            $prodi = Prodi::where('prodi_id', $request->prodi_id)->first();

            if (!$prodi) {
                return redirect()->back()->with('error', 'Data prodi tidak ditemukan');
            }

            $cekKode = UPT::where('kode_upt', $prodi->kode_prodi)->first();
            if ($cekKode) {
                return redirect()->back()->with('error', 'UPT prodi sudah terdaftar');
            }

            $upt = new UPT();
            $upt->upt_id = Str::uuid();
            $upt->kode_upt = $prodi->kode_prodi;
            $upt->nama_upt = $prodi->nama_prodi;
            $upt->kategori_upt = 'Prodi';
            $upt->save();

            return redirect()->route('admin.data.upt')->with('success', 'UPT prodi berhasil ditambahkan');
        }

        $request->validate([
            'nama_upt' => 'required|string|max:255',
            'kode_upt' => 'required|string|max:255|unique:upt,kode_upt',
        ]);

        $upt = new UPT();
        $upt->upt_id = Str::uuid();
        $upt->kode_upt = $request->kode_upt;
        $upt->nama_upt = $request->nama_upt;
        $upt->kategori_upt = 'Unit/Bagian';
        $upt->save();

        return redirect()->route('admin.data.upt')->with('success', 'UPT unit/bagian berhasil ditambahkan');
    }

    public function edit(Request $request)
    {
        $upt = UPT::find($request->upt_id);
        $upt->kode_upt = $request->kode_upt;
        $upt->nama_upt = $request->nama_upt;
        $upt->kategori_upt = $request->kategori_upt;
        $upt->save();
        return redirect()->route('admin.data.upt')->with('success', 'UPT berhasil diubah');
    }

    public function hapus(Request $request)
    {
        $upt = UPT::find($request->upt_id);
        $upt->delete();
        return redirect()->route('admin.data.upt')->with('success', 'UPT berhasil dihapus');
    }
}
