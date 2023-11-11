<?php

namespace App\Http\Controllers;

use App\Services\Certificate\CertificateService;
use App\Services\Order\OrderApproveService;
use App\Services\Order\OrderService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function sign(Request $request, $id)
    {
        $data = app(OrderService::class)->sign($request, $id);
        return response()->json($data);
    }

    public function send(Request $request, $id)
    {
        $data = app(OrderService::class)->send($request, $id);
        return response()->json($data);
    }

    public function video(Request $request, $id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(OrderService::class)->video($request, $id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function approve(Request $request, $id)
    {
        $data = app(OrderApproveService::class)->approve($request, $id);
        return response()->json($data);
    }

    public function decline(Request $request, $id)
    {
        $data = app(OrderApproveService::class)->decline($request, $id);
        return response()->json($data);
    }

    public function revision(Request $request, $id)
    {
        $data = app(OrderApproveService::class)->revision($request, $id);
        return response()->json($data);
    }

    public function executeRun($id)
    {
        $data = app(OrderService::class)->executeRun($id);
        return response()->json($data);
    }

    public function executeClose($id)
    {
        $data = app(OrderService::class)->executeClose($id);
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
