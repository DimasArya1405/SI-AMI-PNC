<?php

namespace App\Http\Controllers\Admin;

use App\DataTables\Admin\Ami\PenugasanDataTable;
use App\DataTables\Admin\PeriodeDataTable;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PenugasanController extends Controller
{
    public function index(PeriodeDataTable $dataTable)
    {
        return $dataTable->render('admin.ami.penugasan');
    }
}
