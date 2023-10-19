<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Category;
use App\Models\Client;
use App\Models\FileType;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\Region;
use App\Services\AuthenticationService;
use App\Services\KapService;
use App\Services\PreOrder\ModeratorPreOrderService;
use App\Services\PreOrder\PreOrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Exception;

class PreOrderController extends Controller
{
    public function get(Request $request)
    {
        $data = app(PreOrderService::class)->getCollection($request);
        return response()->json($data);
    }

    public function getById($id)
    {
        $data = app(PreOrderService::class)->getById($id);
        return response()->json($data);
    }

    public function store(Request $request)
    {
        $data = app(PreOrderService::class)->store($request);
        return response()->json($data);
    }

    public function moderatorApprove(Request $request, $id)
    {
        $data = app(ModeratorPreOrderService::class)->approve($request, $id);
        return response()->json($data);
    }

    public function send(Request $request, $id)
    {
        try {
            $data = app(PreOrderService::class)->send($request, $id);
            $result = ['status' => 200, 'data' => $data];
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result, $result['status']);
    }

    public function delete(Request $request)
    {
        try {
            $result = app(PreOrderService::class)->delete($request);
        } catch (Exception $e) {
            $result = [
                'status' => 500,
                'error' => $e->getMessage()
            ];
        }

        return response()->json($result);
    }


    public function searchFromKap(Request $request){

        $data = ['status', 'failed'];

        $user = app(AuthenticationService::class)->auth();

        $preorder_id = $request->preorder_id;
        $preorder = PreOrderCar::find($preorder_id);


        if($preorder && $preorder->liner_id === $user->id && $preorder->status != 2) {
            $data = app(KapService::class)->get();
        }

        return response()->json($data);
    }
}
