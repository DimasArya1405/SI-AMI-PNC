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
            'prodi_id' => 'nullable|array',
            'prodi_id.*' => 'exists:prodi,prodi_id',
            'nama_upt' => 'nullable|string|max:255',
            'kode_upt' => 'nullable|string|max:255',
        ]);

        if ($request->kategori_upt == 'Prodi') {
            $request->validate([
                'prodi_id' => 'required|array|min:1',
                'prodi_id.*' => 'exists:prodi,prodi_id',
            ]);

            $prodis = Prodi::whereIn('prodi_id', $request->prodi_id)->get();

            if ($prodis->isEmpty()) {
                return redirect()->back()->with('error', 'Data prodi tidak ditemukan');
            }

            $berhasil = 0;
            $sudahAda = 0;

            foreach ($prodis as $prodi) {
                $cekKode = UPT::where('kode_upt', $prodi->kode_prodi)->first();

                if ($cekKode) {
                    $sudahAda++;
                    continue;
                }

                $upt = new UPT();
                $upt->upt_id = Str::uuid();
                $upt->kode_upt = $prodi->kode_prodi;
                $upt->nama_upt = $prodi->nama_prodi;
                $upt->kategori_upt = 'Prodi';
                $upt->save();

                $berhasil++;
            }

            if ($berhasil > 0 && $sudahAda > 0) {
                return redirect()->route('admin.data.upt')
                    ->with('success', "$berhasil UPT prodi berhasil ditambahkan, $sudahAda prodi dilewati karena sudah terdaftar");
            }

            if ($berhasil > 0) {
                return redirect()->route('admin.data.upt')
                    ->with('success', "$berhasil UPT prodi berhasil ditambahkan");
            }

            return redirect()->back()->with('error', 'Semua prodi yang dipilih sudah terdaftar');
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
