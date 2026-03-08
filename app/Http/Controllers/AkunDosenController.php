<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AkunDosenController extends Controller
{
    public function index()
    {
        return view('admin.akun.dosen');
    }
}
