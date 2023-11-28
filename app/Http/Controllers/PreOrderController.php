<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Category;
use App\Models\Client;
use App\Models\FileType;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\Region;
use App\Services\AuthService;
use App\Services\KapService;
use App\Services\Preorder\PreorderApproveService;
use App\Services\Preorder\PreorderSendService;
use App\Services\Preorder\PreorderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class PreOrderController extends Controller
{
    public function get(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(PreorderService::class)->getCollection($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getById($id)
    {
        $data = app(PreorderService::class)->getById($id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        try {
            $data = app(PreorderService::class)->store($request);
            $result = ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result, $result['status']);
    }

    public function send(Request $request, $id)
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

    public function delete($id)
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


    public function approve(Request $request, $id)
    {
        $data = app(PreorderApproveService::class)->approve($request, $id);
        return response()->json($data);
    }

    public function decline(Request $request, $id)
    {
        $data = app(PreorderApproveService::class)->decline($request, $id);
        return response()->json($data);
    }

    public function revision(Request $request, $id)
    {
        $data = app(PreorderApproveService::class)->revision($request, $id);
        return response()->json($data);
    }

    public function searchFromKap(Request $request){
        try {
            $result['status'] = 200;
            $result['data'] = app(KapService::class)->get($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }
    public function kapHistory(Request $request){
        try {
            $result['status'] = 200;
            $result['data'] = app(KapService::class)->history($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }


    public function booking(Request $request, $id)
    {
        $data = app(PreorderService::class)->booking($request, $id);
        return response()->json($data);
    }

}
