<?php

namespace App\Http\Controllers;

use App\Services\KapService;
use App\Services\Preorder\PreorderApproveService;
use App\Services\Preorder\PreorderDataService;
use App\Services\Preorder\PreorderSendService;
use App\Services\Preorder\PreorderService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreOrderController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(PreorderDataService::class)->getCollection($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getById($id): JsonResponse
    {
        return response()->json(app(PreorderDataService::class)->getById($id));
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(PreorderService::class)->store($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function send(Request $request, $id): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(PreorderSendService::class)->send($request, $id);
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
            $result['data'] = app(PreorderService::class)->delete($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function searchFromKap(Request $request): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(KapService::class)->get($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function kapHistory(Request $request): JsonResponse
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(KapService::class)->history($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function approve(Request $request, $id): JsonResponse
    {
        return response()->json(app(PreorderApproveService::class)->approve($request, $id));
    }

    public function decline(Request $request, $id): JsonResponse
    {
        return response()->json(app(PreorderApproveService::class)->decline($request, $id));
    }

    public function revision(Request $request, $id): JsonResponse
    {
        return response()->json(app(PreorderApproveService::class)->revision($request, $id));
    }

}
