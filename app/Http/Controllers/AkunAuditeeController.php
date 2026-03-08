<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AkunAuditeeController extends Controller
{
        public function index()
    {
        return view('admin.akun.auditee');
    }
}

