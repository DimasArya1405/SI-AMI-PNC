<?php

namespace App\Http\Controllers;

use App\Models\Auditee;
use App\Models\Auditor;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
        $auditor = Auditor::count();
        $auditee = Auditee::count();
        return view('admin.dashboard', compact('auditor', 'auditee'));
    }
}
