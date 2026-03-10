<?php

namespace App\Http\Controllers\Admin\Akun;

use App\DataTables\Admin\Akun\AuditeeDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuditeeController extends Controller
{
    public function index(AuditeeDataTable $dataTable)
    {
        return $dataTable->render('admin.akun.auditee');
    }
}
