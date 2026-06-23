<?php

namespace App\Http\Controllers\KepalaP4mp;

use App\Http\Controllers\Controller;
use App\Models\RingkasanKondisiAudit;
use Illuminate\Http\Request;

class RkaController extends Controller
{
    public function acc($id){
        $rka = RingkasanKondisiAudit::find($id);
        $rka->acc_p4mp = '1';
        $rka->acc_p4mp_at = now();
        $rka->save();
        return back()->with('success', 'RKA Berhasil di Tanda Tangani!');
    }
}
