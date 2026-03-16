<?php

namespace App\Http\Controllers\Admin\Akun;

use App\DataTables\Admin\Akun\AuditorDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuditorController extends Controller
{
    public function index(AuditorDataTable $dataTable)
    {
        $prodi = Prodi::all();
        return $dataTable->render('admin.akun.auditor', compact('prodi'));
    }
    public function tambah(Request $request)
    {
        $cekNip = Auditor::where('nip', $request->nip)->exists();
        if ($cekNip) {
            return redirect('/admin/akun/auditor')
                ->with('error', 'NIP sudah terdaftar!');
        }

        $cekEmail = User::where('email', $request->email)->exists();
        if ($cekEmail) {
            return redirect('/admin/akun/auditor')
                ->with('error', 'Email sudah terdaftar!');
        }

        $newUser = new User;
        $newUser->id = Str::uuid();
        $newUser->name = $request->nama;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->nama);
        $newUser->role = 'auditor';
        $newUser->save();

        $auditor = new Auditor;
        $auditor->auditor_id = Str::uuid();
        $auditor->user_id = $newUser->id;
        $auditor->nip = $request->nip;
        $auditor->nama_lengkap = $request->nama;
        $auditor->jabatan = $request->jabatan;
        $auditor->prodi_id = $request->prodi;
        $auditor->no_telp = $request->no_telp;
        $auditor->email = $request->email;
        $auditor->status_aktif = '1';
        $auditor->save();
        return redirect('/admin/akun/auditor')->with('success', 'Data Auditor Berhasil Ditambahkan!');
    }
    public function edit(Request $request)
    {
        $auditor = Auditor::find($request->auditor_id);
        $auditor->nip = $request->nip;
        $auditor->nama_lengkap = $request->nama;
        $auditor->jabatan = $request->jabatan;
        $auditor->prodi_id = $request->prodi;
        $auditor->no_telp = $request->no_telp;
        $auditor->email = $request->email;
        $auditor->save();
        return redirect('/admin/akun/auditor')->with('success', 'Data Auditor Berhasil Diubah!');
    }
    public function hapus(Request $request)
    {
        $auditor = Auditor::find($request->auditor_id);
        $auditor->delete();
        return redirect('/admin/akun/auditor')->with('success', 'Data Auditor Berhasil Dihapus!');
    }
}
