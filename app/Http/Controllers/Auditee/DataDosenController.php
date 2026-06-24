<?php

namespace App\Http\Controllers\Auditee;

use App\DataTables\Admin\Akun\DosenDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\Dosen;
use App\Models\Prodi;
use App\Models\UPT;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class DataDosenController extends Controller
{
    public function index(DosenDataTable $dataTable)
    {
        $userId = Auth::id();
        $auditee = Auditee::where('user_id', $userId)->first();
        $prodi = Prodi::all();
        $upt = UPT::orderBy('kode_upt')->get();
        return $dataTable->render('auditee.dosen', compact('prodi', 'upt', 'auditee'));
    }

    public function tambah(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'no_telp' => 'nullable|string|max:255',
        ], [
            'nip.required' => 'NIP wajib diisi!',
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus berupa alamat email yang valid!',
        ]);

        $nip = trim($validated['nip']);
        $email = strtolower(trim($validated['email']));

        if (Dosen::where('nip', $nip)->exists()) {
            return redirect('/auditee/dosen')
                ->with('error', 'NIP sudah terdaftar!');
        }

        if (User::where('email', $email)->exists() || Dosen::where('email', $email)->exists()) {
            return redirect('/auditee/dosen')
                ->with('error', 'Email sudah terdaftar!');
        }

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();

        $dosenTerhapus = Dosen::onlyTrashed()
            ->where(function ($query) use ($nip, $email) {
                $query->where('nip', $nip)
                    ->orWhere('email', $email);
            })
            ->first();

        if ($dosenTerhapus) {
            $user = User::withTrashed()->find($dosenTerhapus->user_id);

            if (!$user) {
                return redirect('/auditee/dosen')
                    ->with('error', 'Data dosen lama ditemukan, tetapi akun pengguna terkait tidak ditemukan.');
            }

            $emailDipakaiAkunLain = User::withTrashed()
                ->where('email', $email)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($emailDipakaiAkunLain) {
                return redirect('/auditee/dosen')
                    ->with('error', 'Email sudah terdaftar pada akun lain!');
            }

            DB::transaction(function () use ($dosenTerhapus, $user, $auditee, $validated, $nip, $email) {
                $user->restore();
                $user->name = $validated['nama'];
                $user->email = $email;
                $user->password = Hash::make($nip);
                $user->role = 'dosen';
                $user->save();

                $dosenTerhapus->restore();
                $dosenTerhapus->nip = $nip;
                $dosenTerhapus->nama_lengkap = $validated['nama'];
                $dosenTerhapus->jabatan = $validated['jabatan'] ?? null;
                $dosenTerhapus->upt_id = $auditee->upt_id;
                $dosenTerhapus->prodi_id = null;
                $dosenTerhapus->no_telp = $validated['no_telp'] ?? null;
                $dosenTerhapus->email = $email;
                $dosenTerhapus->status_aktif = '1';
                $dosenTerhapus->save();
            });

            return redirect('/auditee/dosen')->with('success', 'Data Dosen yang pernah dihapus berhasil dipulihkan!');
        }

        if (User::onlyTrashed()->where('email', $email)->exists()) {
            return redirect('/auditee/dosen')
                ->with('error', 'Email sudah pernah terdaftar pada akun yang dihapus. Gunakan email lain atau hubungi admin.');
        }

        DB::transaction(function () use ($auditee, $validated, $nip, $email) {
            $newUser = new User();
            $newUser->id = Str::uuid();
            $newUser->name = $validated['nama'];
            $newUser->email = $email;
            $newUser->password = Hash::make($nip);
            $newUser->role = 'dosen';
            $newUser->save();

            $dosen = new Dosen;
            $dosen->dosen_id = Str::uuid();
            $dosen->user_id = $newUser->id;
            $dosen->nip = $nip;
            $dosen->nama_lengkap = $validated['nama'];
            $dosen->jabatan = $validated['jabatan'] ?? null;
            $dosen->upt_id = $auditee->upt_id;
            $dosen->prodi_id = null;
            $dosen->no_telp = $validated['no_telp'] ?? null;
            $dosen->email = $email;
            $dosen->status_aktif = '1';
            $dosen->save();
        });

        return redirect('/auditee/dosen')->with('success', 'Data Dosen Berhasil Ditambahkan!');
    }

    public function edit(Request $request)
    {
        $validated = $request->validate([
            'dosen_id' => 'required|exists:dosen,dosen_id',
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'jabatan' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'no_telp' => 'nullable|string|max:255',
        ], [
            'nip.required' => 'NIP wajib diisi!',
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus berupa alamat email yang valid!',
        ]);

        $auditee = Auditee::where('user_id', Auth::id())->firstOrFail();
        $nip = trim($validated['nip']);
        $email = strtolower(trim($validated['email']));
        
        $dosen = Dosen::findOrFail($validated['dosen_id']);

        $cekNip = Dosen::withTrashed()
            ->where('nip', $nip)
            ->where('dosen_id', '!=', $validated['dosen_id'])
            ->exists();

        if ($cekNip) {
            return redirect('/auditee/dosen')
                ->with('error', 'NIP sudah terdaftar!');
        }

        $cekEmailUser = User::withTrashed()
            ->where('email', $email)
            ->where('id', '!=', $dosen->user_id)
            ->exists();

        if ($cekEmailUser) {
            return redirect('/auditee/dosen')
                ->with('error', 'Email sudah terdaftar!');
        }

        $cekEmailDosen = Dosen::withTrashed()
            ->where('email', $email)
            ->where('dosen_id', '!=', $validated['dosen_id'])
            ->exists();

        if ($cekEmailDosen) {
            return redirect('/auditee/dosen')
                ->with('error', 'Email sudah terdaftar!');
        }

        $dosen->nip = $nip;
        $dosen->nama_lengkap = $validated['nama'];
        $dosen->jabatan = $validated['jabatan'] ?? null;
        $dosen->upt_id = $auditee->upt_id;
        $dosen->prodi_id = null;
        $dosen->no_telp = $validated['no_telp'] ?? null;
        $dosen->email = $email;
        $dosen->save();

        $user = User::findOrFail($dosen->user_id);
        $user->name = $validated['nama'];
        $user->email = $email;
        $user->password = Hash::make($nip);
        $user->save();
        return redirect('/auditee/dosen')->with('success', 'Data Dosen Berhasil Diubah!');
    }

    public function hapus(Request $request)
    {
        $dosen = Dosen::findOrFail($request->dosen_id);
        $user = User::find($dosen->user_id);

        DB::transaction(function () use ($dosen, $user) {
            if ($user) {
                $user->delete();
            }

            $dosen->delete();
        });

        return redirect('/auditee/dosen')->with('success', 'Data Dosen Berhasil Dihapus!');
    }
}
