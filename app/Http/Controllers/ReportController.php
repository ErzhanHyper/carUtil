<?php

namespace App\Http\Controllers;

use App\Exports\ActionExport;
use App\Exports\CertExport;
use App\Exports\ExchangeExport;
use App\Exports\SellExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{

    public function getCert(Request $request)
    {
        return Excel::download(new CertExport($request), 'certificate.xlsx');
    }

    public function getSell(Request $request)
    {
        return Excel::download(new SellExport($request), 'sell.xlsx');
    }

    public function getExchange(Request $request)
    {
        return Excel::download(new ExchangeExport($request), 'exchange.xlsx');
    }

    public function getAction(Request $request)
    {
        return Excel::download(new ActionExport($request), 'action.xlsx');
    }

}
