<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function buka(Request $request, string $id): RedirectResponse
    {
        $notifikasi = $request->user()
            ->notifications()
            ->where('id', $id)
            ->firstOrFail();

        $notifikasi->markAsRead();

        return redirect($notifikasi->data['url'] ?? url()->previous());
    }

    public function bacaSemua(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }
}
