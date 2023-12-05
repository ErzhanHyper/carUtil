<?php

namespace App\Http\Controllers;

use App\Services\Document\DocumentTransferService;
use App\Services\Transfer\TransferService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferOrderController extends Controller
{
    public function get(Request $request): JsonResponse
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

    public function getById($id): JsonResponse
    {
        try {
            $result['status'] = 200;
            $item = app(TransferService::class)->getById($id);
            $itemData = json_decode($item->data);
            $result['data'] = [
                'item' => $item,
                'contract' => $item->closed === 1 ? app(DocumentTransferService::class)->setData($id, null) : null
            ];
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getCurrent(Request $request): JsonResponse
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

    public function store(Request $request): JsonResponse
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

    public function sign(Request $request, $id): JsonResponse
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

    public function delete($id): JsonResponse
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
