<?php

namespace App\Http\Controllers\Admin\Akun;

use App\DataTables\Admin\Akun\AuditeeDataTable;
use App\Http\Controllers\Controller;
use App\Models\Auditee;
use App\Models\UPT;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuditeeController extends Controller
{
    public function index(AuditeeDataTable $dataTable)
    {
        $upt = UPT::all();
        return $dataTable->render('admin.akun.auditee', compact('upt'));
    }

    public function tambah(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'required|string|max:255',
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'upt' => 'required|exists:upt,upt_id',
            'no_telp' => 'nullable|string|max:255',
        ], [
            'nip.required' => 'NIP wajib diisi!',
            'nama.required' => 'Nama wajib diisi!',
            'email.required' => 'Email wajib diisi!',
            'email.email' => 'Email harus berupa alamat email yang valid!',
            'upt.required' => 'UPT wajib dipilih!',
            'upt.exists' => 'UPT yang dipilih tidak valid!',
        ]);

        $nip = trim($validated['nip']);
        $email = strtolower(trim($validated['email']));

        if (Auditee::where('nip', $nip)->exists()) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'NIP sudah terdaftar!');
        }

        if (User::where('email', $email)->exists() || Auditee::where('email', $email)->exists()) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'Email sudah terdaftar!');
        }

        if (Auditee::where('upt_id', $validated['upt'])->exists()) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'UPT tersebut sudah memiliki akun auditee!');
        }

        $auditeeTerhapus = Auditee::onlyTrashed()
            ->where(function ($query) use ($nip, $email, $validated) {
                $query->where('nip', $nip)
                    ->orWhere('email', $email)
                    ->orWhere('upt_id', $validated['upt']);
            })
            ->first();

        if ($auditeeTerhapus) {
            $user = User::withTrashed()->find($auditeeTerhapus->user_id);

            if (!$user) {
                return redirect('/admin/akun/auditee')
                    ->with('error', 'Data auditee lama ditemukan, tetapi akun pengguna terkait tidak ditemukan.');
            }

            $emailDipakaiAkunLain = User::withTrashed()
                ->where('email', $email)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($emailDipakaiAkunLain) {
                return redirect('/admin/akun/auditee')
                    ->with('error', 'Email sudah terdaftar pada akun lain!');
            }

            DB::transaction(function () use ($auditeeTerhapus, $user, $validated, $nip, $email) {
                $user->restore();
                $user->name = $validated['nama'];
                $user->email = $email;
                $user->password = Hash::make($nip);
                $user->role = 'auditee';
                $user->save();

                $auditeeTerhapus->restore();
                $auditeeTerhapus->nip = $nip;
                $auditeeTerhapus->nama_lengkap = $validated['nama'];
                $auditeeTerhapus->upt_id = $validated['upt'];
                $auditeeTerhapus->no_telp = $validated['no_telp'] ?? null;
                $auditeeTerhapus->email = $email;
                $auditeeTerhapus->status_aktif = '1';
                $auditeeTerhapus->save();
            });

            return redirect('/admin/akun/auditee')->with('success', 'Data Auditee yang pernah dihapus berhasil dipulihkan!');
        }

        if (User::onlyTrashed()->where('email', $email)->exists()) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'Email sudah pernah terdaftar pada akun yang dihapus. Gunakan email lain atau hubungi admin.');
        }

        DB::transaction(function () use ($validated, $nip, $email) {
            $newUser = new User;
            $newUser->id = Str::uuid();
            $newUser->name = $validated['nama'];
            $newUser->email = $email;
            $newUser->password = Hash::make($nip);
            $newUser->role = 'auditee';
            $newUser->save();

            $auditee = new Auditee;
            $auditee->auditee_id = Str::uuid();
            $auditee->user_id = $newUser->id;
            $auditee->nip = $nip;
            $auditee->nama_lengkap = $validated['nama'];
            $auditee->upt_id = $validated['upt'];
            $auditee->no_telp = $validated['no_telp'] ?? null;
            $auditee->email = $email;
            $auditee->status_aktif = '1';
            $auditee->save();
        });

        return redirect('/admin/akun/auditee')->with('success', 'Data Auditee Berhasil Ditambahkan!');
    }

    public function edit(Request $request)
    {
        $request->validate([
            'auditee_id' => 'required',
            'nip' => 'required',
            'nama' => 'required',
            'email' => 'required|email',
            'upt' => 'required',
            'no_telp' => 'nullable',
        ], [
            'upt.required' => 'UPT wajib dipilih!',
        ]);

        $auditee = Auditee::findOrFail($request->auditee_id);
        $nip = trim($request->nip);
        $email = strtolower(trim($request->email));

        $cekNip = Auditee::withTrashed()
            ->where('nip', $nip)
            ->where('auditee_id', '!=', $request->auditee_id)
            ->exists();

        if ($cekNip) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'NIP sudah terdaftar!');
        }

        $cekEmail = User::withTrashed()
            ->where('email', $email)
            ->where('id', '!=', $auditee->user_id)
            ->exists();

        if ($cekEmail) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'Email sudah terdaftar!');
        }

        $cekUpt = Auditee::where('upt_id', $request->upt)
            ->where('auditee_id', '!=', $request->auditee_id)
            ->exists();

        if ($cekUpt) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'UPT tersebut sudah memiliki akun auditee!');
        }

        $cekEmailAuditee = Auditee::withTrashed()
            ->where('email', $email)
            ->where('auditee_id', '!=', $request->auditee_id)
            ->exists();

        if ($cekEmailAuditee) {
            return redirect('/admin/akun/auditee')
                ->with('error', 'Email sudah terdaftar!');
        }

        $auditee->nip = $nip;
        $auditee->nama_lengkap = $request->nama;
        $auditee->upt_id = $request->upt;
        $auditee->no_telp = $request->no_telp;
        $auditee->email = $email;
        $auditee->save();

        $user = User::findOrFail($auditee->user_id);
        $user->name = $request->nama;
        $user->email = $email;
        $user->password = Hash::make($nip);
        $user->save();

        return redirect('/admin/akun/auditee')
            ->with('success', 'Data Auditee Berhasil Diubah!');
    }

    public function hapus(Request $request)
    {
        $auditee = Auditee::findOrFail($request->auditee_id);
        $user = User::find($auditee->user_id);

        DB::transaction(function () use ($auditee, $user) {
            if ($user) {
                $user->delete();
            }

            $auditee->delete();
        });

        return redirect('/admin/akun/auditee')->with('success', 'Data Auditee Berhasil Dihapus!');
    }

    public function aktivasi(Request $request)
    {
        $auditee = Auditee::find($request->auditee_id);

        if ($auditee->status_aktif == 1) {
            $auditee->status_aktif = 0;
        } else {
            $auditee->status_aktif = 1;
        }

        $auditee->save();
        return redirect('/admin/akun/auditee')->with('success', 'Status berhasil diubah!');
    }
}
