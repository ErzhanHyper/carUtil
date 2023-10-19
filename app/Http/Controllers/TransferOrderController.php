<?php

namespace App\Http\Controllers;

use App\Services\Transfer\TransferService;
use Illuminate\Http\Request;

class TransferOrderController extends Controller
{
    public function get(Request $request)
    {
        $data = app(TransferService::class)->getCollection($request);
        return response()->json($data);
    }

    public function getById($id)
    {
        $data = app(TransferService::class)->getById($id);
        return response()->json($data);
    }

    public function getCurrent(Request $request)
    {
        $data = app(TransferService::class)->getCurrenCollection($request);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = app(TransferService::class)->store($request);
        return response()->json($data);
    }

    public function sign(Request $request)
    {
        $data = app(TransferService::class)->sign($request);
        return response()->json($data);
    }

    public function pfs(Request $request)
    {
        $data = app(TransferService::class)->pfs($request);
        return response()->json($data);
    }

    public function close(Request $request)
    {
        $data = app(TransferService::class)->close($request);
        return response()->json($data);
    }
}
