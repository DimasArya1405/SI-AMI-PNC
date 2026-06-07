<?php

namespace App\Http\Controllers\Admin\Akun;

use App\DataTables\Admin\Akun\KepalaP4mpDataTable;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class KepalaP4mpController extends Controller
{
    public function index(KepalaP4mpDataTable $dataTable)
    {
        return $dataTable->render('admin.akun.kepala-p4mp');
    }

    public function tambah(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->id = (string) Str::uuid();
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->password = Hash::make($validated['password']);
        $user->role = 'kepala_p4mp';
        $user->save();

        return back()->with('success', 'Akun Kepala P4MP berhasil ditambahkan.');
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
}
