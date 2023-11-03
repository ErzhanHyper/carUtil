<?php

namespace App\Http\Controllers;

use App\Services\Document\DocumentTransferService;
use App\Services\Transfer\TransferService;
use Exception;
use Illuminate\Http\Request;

class TransferOrderController extends Controller
{
    public function get(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferService::class)->getCollection($request->params);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getById($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferService::class)->getById($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getCurrent(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferService::class)->getCurrenCollection($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function store(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferService::class)->store($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function sign(Request $request, $id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferService::class)->sign($request, $id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);

    }

    public function delete($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferService::class)->close($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

}
