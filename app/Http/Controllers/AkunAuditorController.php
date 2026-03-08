<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AkunAuditorController extends Controller
{
    public function index()
    {
        return view('admin.akun.auditor');
    }
}
