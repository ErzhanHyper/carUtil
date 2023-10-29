<?php

namespace App\Http\Controllers;

use App\Services\Certificate\CertificateService;
use App\Services\Order\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function get(Request $request)
    {
        $data = app(OrderService::class)->getCollection($request);
        return response()->json($data);
    }

    public function getById($id)
    {
        $data = app(OrderService::class)->getById($id);
        return response()->json($data);
    }

    public function sign(Request $request)
    {
        $data = app(OrderService::class)->sign($request);
        return response()->json($data);
    }

    public function approve(Request $request)
    {
        $data = app(OrderService::class)->approve($request);
        return response()->json($data);
    }

    public function decline(Request $request)
    {
        $data = app(OrderService::class)->decline($request);
        return response()->json($data);
    }

    public function revision(Request $request)
    {
        $data = app(OrderService::class)->revision($request);
        return response()->json($data);
    }

    public function cert(Request $request)
    {
        $data = app(CertificateService::class)->storeCert($request);
        return response()->json($data);
    }

    public function generateCert(Request $request)
    {
        $data = app(CertificateService::class)->generateCert($request);
        return response()->json($data);
    }
}
