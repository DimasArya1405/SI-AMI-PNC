<?php

namespace App\Http\Controllers\Admin\Akun;

use App\DataTables\Admin\Akun\AuditeeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuditeeController extends Controller
{
    public function index(AuditeeDataTable $dataTable)
    {
        $prodi = Prodi::all();
        return $dataTable->render('admin.akun.auditee', compact('prodi'));
    }

    public function tambah(Request $request) {
        $cek = Auditee::where('nip', $request->nip)->exists();
        if ($cek) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'NIP sudah terdaftar!');
        }

        $newUser = new User;
        $newUser->id = Str::uuid();
        $newUser->name = $request->nama;
        $newUser->email = $request->email;
        $newUser->password = Hash::make($request->nip);
        $newUser->role = 'auditee';
        $newUser->save();

        $auditee = new Auditee;
        $auditee->auditee_id = Str::uuid();
        $auditee->user_id = $newUser->id;
        $auditee->nip = $request->nip;
        $auditee->nama_lengkap = $request->nama;
        $auditee->jabatan = $request->jabatan;
        $auditee->prodi_id = $request->prodi;
        $auditee->no_telp = $request->no_telp;
        $auditee->email = $request->email;
        $auditee->status_aktif = '1';
        $auditee->save();
        return redirect('/admin/akun/auditee')->with('success', 'Data Auditee Berhasil Ditambahkan!');
    }

    public function edit(Request $request) {
        $auditee = Auditee::find($request->auditee_id);
        $auditee->nip = $request->nip;
        $auditee->nama_lengkap = $request->nama;
        $auditee->jabatan = $request->jabatan;
        $auditee->prodi_id = $request->prodi;
        $auditee->no_telp = $request->no_telp;
        $auditee->email = $request->email;
        $auditee->save();

        $user = User::find($auditee->user_id);
        $user->name = $request->nama;
        $user->email = $request->email;
        $user->password = Hash::make($request->nip);
        $user->save();
        return redirect('/admin/akun/auditee')->with('success', 'Data Auditee Berhasil Diubah!');
    }

    public function hapus(Request $request) {
        $auditee = Auditee::find($request->auditee_id);
        $user = User::where('email', $request->email)->first();
        $user->delete();
        $auditee->delete();
        return redirect('/admin/akun/auditee')->with('success', 'Data Auditee Berhasil Dihapus!');
    }

    public function aktivasi(Request $request) {
    $auditee = Auditee::find($request->auditee_id);

    if ($auditee->status_aktif == 1) {
        $auditee->status_aktif = 0;
    } else {
        $auditee->status_aktif = 1;
    }

    $auditee->save();
    return redirect('/admin/akun/auditee')->with('success','Status berhasil diubah!');
    }
}
