<?php

namespace App\Http\Controllers;

use App\Services\Transfer\TransferDealService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransferDealController extends Controller
{

    public function get(Request $request): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferDealService::class)->getCollection($request);
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
            $result['data'] = app(TransferDealService::class)->store($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function accept($id): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferDealService::class)->accept($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function close($id): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferDealService::class)->close($id);
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
            $result['data'] = app(TransferDealService::class)->delete($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }
}
