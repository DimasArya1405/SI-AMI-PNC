<?php

namespace App\Http\Middleware;

use App\Models\Auditee;
use App\Models\Auditor;
use App\Models\Dosen;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekStatusAktif
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Admin selalu boleh akses
            if ($user->role !== 'admin') {
                $statusAktif = false;
                $dataRole = null;

                if ($user->role === 'auditee') {
                    $dataRole = Auditee::where('user_id', $user->id)->first();
                } elseif ($user->role === 'auditor') {
                    $dataRole = Auditor::where('user_id', $user->id)->first();
                } elseif ($user->role === 'dosen') {
                    $dataRole = Dosen::where('user_id', $user->id)->first();
                }

                $statusAktif = (int) ($dataRole->status_aktif ?? 0) === 1;

                if (!$statusAktif) {
                    Auth::guard('web')->logout();

                    $request->session()->invalidate();
                    $request->session()->regenerateToken();

                    return redirect()->route('login')->withErrors([
                        'email' => 'Akun Anda sudah tidak aktif. Silakan hubungi admin.',
                    ]);
                }
            }
        }

        return $next($request);
    }
}