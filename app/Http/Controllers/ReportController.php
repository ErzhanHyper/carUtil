<?php

namespace App\Http\Controllers;

use App\Exports\CertExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{

    public function getCert(Request $request)
    {
        return Excel::download(new CertExport($request), 'certificate.xlsx');
    }

}
