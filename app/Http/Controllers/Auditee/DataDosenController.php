<?php

namespace App\Http\Controllers\Auditee;

use App\DataTables\Admin\Akun\DosenDataTable;
use App\Http\Controllers\Controller;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class DataDosenController extends Controller
{
    public function index(DosenDataTable $dataTable)
    {
        $prodi = Prodi::all();
        return $dataTable->render('auditee.dosen', compact('prodi'));
    }

    public function tambah(Request $request) {
        $cek = Dosen::where('nip', $request->nip)->exists();
        if ($cek) {
            return redirect('/auditee/dosen')
                ->with('error', 'NIP sudah terdaftar!');
        }

        $newUser = new User();
        $newUser->id = Str::uuid();
        $newUser->name = $request->nama;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->nip);
        $newUser->role = 'dosen';
        $newUser->save();

        $dosen = new Dosen;
        $dosen->dosen_id = Str::uuid();
        $dosen->user_id = $newUser->id;
        $dosen->nip = $request->nip;
        $dosen->nama_lengkap = $request->nama;
        $dosen->jabatan = $request->jabatan;
        $dosen->prodi_id = $request->prodi;
        $dosen->no_telp = $request->no_telp;
        $dosen->email = $request->email;
        $dosen->status_aktif = '1';
        $dosen->save();
        return redirect('/auditee/dosen')->with('success', 'Data Dosen Berhasil Ditambahkan!');
    }

    public function edit(Request $request) {
        $dosen = Dosen::find($request->dosen_id);
        $dosen->nip = $request->nip;
        $dosen->nama_lengkap = $request->nama;
        $dosen->jabatan = $request->jabatan;
        $dosen->prodi_id = $request->prodi;
        $dosen->no_telp = $request->no_telp;
        $dosen->email = $request->email;
        $dosen->save();

        $user = User::find($dosen->user_id);
        $user->name = $request->nama;
        $user->email = $request->email;
        $user->password = Hash::make($request->nip);
        $user->save();
        return redirect('/auditee/dosen')->with('success', 'Data Dosen Berhasil Diubah!');
    }

    public function hapus(Request $request) {
        $dosen = Dosen::find($request->dosen_id);
        $user = User::where('email', $request->email)->first();
        $user->delete();
        $dosen->delete();
        return redirect('/auditee/dosen')->with('success', 'Data Dosen Berhasil Dihapus!');
    }
}
