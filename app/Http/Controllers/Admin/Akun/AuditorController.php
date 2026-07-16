<?php

namespace App\Http\Controllers\Admin\Akun;

use App\DataTables\Admin\Akun\AuditorDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditor;
use App\Models\Prodi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $request->validate([
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'prodi' => 'nullable|exists:prodi,prodi_id',
            'email' => 'required|email|max:255',
            'no_telp' => 'nullable|string|max:255',
        ], [
            'nip.required' => 'NIP wajib diisi!',
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus berupa alamat email yang valid!',
        ]);

        $nip = trim($request->nip);
        $email = strtolower(trim($request->email));

        $cekNip = Auditor::withTrashed()->where('nip', $nip)->exists();
        if ($cekNip) {
            return redirect('/admin/akun/auditor')
                ->with('error', 'NIP sudah terdaftar!');
        }

        $cekEmail = User::withTrashed()->where('email', $email)->exists()
            || Auditor::withTrashed()->where('email', $email)->exists();

        if ($cekEmail) {
            return redirect('/admin/akun/auditor')
                ->with('error', 'Email sudah terdaftar!');
        }

        DB::transaction(function () use ($request, $nip, $email) {
            $newUser = new User;
            $newUser->id = Str::uuid();
            $newUser->name = $request->nama;
            $newUser->email = $email;
            $newUser->password = Hash::make($nip);
            $newUser->role = 'auditor';
            $newUser->save();

            $auditor = new Auditor;
            $auditor->auditor_id = Str::uuid();
            $auditor->user_id = $newUser->id;
            $auditor->nip = $nip;
            $auditor->nama_lengkap = $request->nama;
            $auditor->jabatan = $request->jabatan;
            $auditor->prodi_id = $request->prodi;
            $auditor->no_telp = $request->no_telp;
            $auditor->email = $email;
            $auditor->status_aktif = '1';
            $auditor->save();
        });

        return redirect('/admin/akun/auditor')->with('success', 'Data Auditor Berhasil Ditambahkan!');
    }
    public function edit(Request $request)
    {
        $request->validate([
            'auditor_id' => 'required',
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'prodi' => 'nullable|exists:prodi,prodi_id',
            'email' => 'required|email|max:255',
            'no_telp' => 'nullable|string|max:255',
        ], [
            'nip.required' => 'NIP wajib diisi!',
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus berupa alamat email yang valid!',
        ]);

        $auditor = Auditor::findOrFail($request->auditor_id);
        $nip = trim($request->nip);
        $email = strtolower(trim($request->email));

        $cekNip = Auditor::withTrashed()
            ->where('nip', $nip)
            ->where('auditor_id', '!=', $auditor->auditor_id)
            ->exists();

        if ($cekNip) {
            return redirect('/admin/akun/auditor')
                ->with('error', 'NIP sudah terdaftar!');
        }

        $cekEmailUser = User::withTrashed()
            ->where('email', $email)
            ->where('id', '!=', $auditor->user_id)
            ->exists();

        $cekEmailAuditor = Auditor::withTrashed()
            ->where('email', $email)
            ->where('auditor_id', '!=', $auditor->auditor_id)
            ->exists();

        if ($cekEmailUser || $cekEmailAuditor) {
            return redirect('/admin/akun/auditor')
                ->with('error', 'Email sudah terdaftar!');
        }

        DB::transaction(function () use ($request, $auditor, $nip, $email) {
            $user = User::withTrashed()->find($auditor->user_id);

            if (!$user) {
                $user = new User;
                $user->id = Str::uuid();
                $user->password = Hash::make($nip);
                $auditor->user_id = $user->id;
            } elseif ($user->trashed()) {
                $user->restore();
            }

            $user->name = $request->nama;
            $user->email = $email;
            $user->role = 'auditor';
            $user->save();

            $auditor->nip = $nip;
            $auditor->nama_lengkap = $request->nama;
            $auditor->jabatan = $request->jabatan;
            $auditor->prodi_id = $request->prodi;
            $auditor->no_telp = $request->no_telp;
            $auditor->email = $email;
            $auditor->save();
        });

        return redirect('/admin/akun/auditor')->with('success', 'Data Auditor Berhasil Diubah!');
    }
    public function hapus(Request $request)
    {
        $auditor = Auditor::findOrFail($request->auditor_id);
        $user = User::find($auditor->user_id);

        DB::transaction(function () use ($auditor, $user) {
            if ($user) {
                $user->delete();
            }

            $auditor->delete();
        });

        return redirect('/admin/akun/auditor')->with('success', 'Data Auditor Berhasil Dihapus!');
    }
}
