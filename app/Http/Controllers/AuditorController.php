<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditorController extends Controller
{
    public function index()
    {
        return view('auditor.dashboard');
    }
}
