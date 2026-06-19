<?php

namespace App\Http\Controllers\Admin\Akun;

use App\DataTables\Admin\Akun\KepalaP4mpDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KepalaP4mpController extends Controller
{
    public function index(KepalaP4mpDataTable $dataTable)
    {
        $this->pastikanHanyaSatuAktif();

        $jumlahKepalaP4mp = User::where('role', 'kepala_p4mp')->count();
        $jumlahAktif = User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->count();

        return $dataTable->render('admin.akun.kepala-p4mp', compact('jumlahKepalaP4mp', 'jumlahAktif'));
    }

    public function tambah(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $sudahAdaYangAktif = User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->exists();

        $user = new User();
        $user->id = (string) Str::uuid();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'kepala_p4mp';
        $user->status_aktif = !$sudahAdaYangAktif;
        $user->save();

        $status = $user->status_aktif ? 'aktif' : 'tidak aktif';

        return back()->with('success', "Akun Kepala P4MP berhasil ditambahkan dengan status {$status}.");
    }

    public function edit(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->user_id,
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::where('role', 'kepala_p4mp')
            ->where('id', $validated['user_id'])
            ->firstOrFail();

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Akun Kepala P4MP berhasil diperbarui.');
    }

    public function hapus(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        User::where('role', 'kepala_p4mp')
            ->where('id', $validated['user_id'])
            ->firstOrFail()
            ->delete();

        return back()->with('success', 'Akun Kepala P4MP berhasil dihapus.');
    }

    public function aktivasi(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        $user = User::where('role', 'kepala_p4mp')
            ->where('id', $validated['user_id'])
            ->firstOrFail();

        if ($user->status_aktif) {
            $user->status_aktif = false;
            $user->save();

            return back()->with('success', 'Akun Kepala P4MP berhasil dinonaktifkan.');
        }

        DB::transaction(function () use ($user) {
            User::where('role', 'kepala_p4mp')
                ->where('id', '!=', $user->id)
                ->update(['status_aktif' => false]);

            $user->status_aktif = true;
            $user->save();
        });

        return back()->with('success', 'Akun Kepala P4MP berhasil diaktifkan. Akun Kepala P4MP lain otomatis dinonaktifkan.');
    }

    private function pastikanHanyaSatuAktif(): void
    {
        $akunAktif = User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->orderByDesc('updated_at')
            ->get();

        if ($akunAktif->count() <= 1) {
            return;
        }

        User::where('role', 'kepala_p4mp')
            ->where('status_aktif', true)
            ->where('id', '!=', $akunAktif->first()->id)
            ->update(['status_aktif' => false]);
    }
}
