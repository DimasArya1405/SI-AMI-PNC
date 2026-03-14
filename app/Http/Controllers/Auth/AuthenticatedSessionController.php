<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Auditee;
use App\Models\Auditor;
use App\Models\Dosen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // Cek status aktif untuk role selain admin
        if ($user->role !== 'admin') {
            $statusAktif = false;

            if ($user->role === 'auditor') {
                $auditor = Auditor::where('user_id', $user->id)->first();
                $statusAktif = $auditor?->status_aktif ?? false;
            } elseif ($user->role === 'auditee') {
                $auditee = Auditee::where('user_id', $user->id)->first();
                $statusAktif = $auditee?->status_aktif ?? false;
            } elseif ($user->role === 'dosen') {
                $dosen = Dosen::where('user_id', $user->id)->first();
                $statusAktif = $dosen?->status_aktif ?? false;
            }

            if (!$statusAktif) {
                Auth::guard('web')->logout();

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'email' => 'Akun Anda tidak aktif. Silakan hubungi admin.',
                ]);
            }
        }

        // Logika redirect berdasarkan role
        $url = '';
        if ($request->user()->role === 'admin') {
            $url = 'admin/dashboard';
        } elseif ($request->user()->role === 'auditor') {
            $url = 'auditor/dashboard';
        } elseif ($request->user()->role === 'auditee') {
            $url = 'auditee/dashboard';
        } else {
            $url = 'dosen/dashboard';
        }

        return redirect()->intended($url);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
