<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AuditeeController extends Controller
{
    public function index()
    {
        return view('auditee.dashboard');
    }
}
