<?php

namespace App\Http\Controllers;

use App\Services\BookingOrder\BookingOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingOrderController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        $data = app(BookingOrderService::class)->get($request);
        return response()->json($data);
    }

    public function store(Request $request): JsonResponse
    {
        $data = app(BookingOrderService::class)->store($request);
        return response()->json($data);
    }

    public function delete(Request $request): JsonResponse
    {
        $data = app(BookingOrderService::class)->delete($request);
        return response()->json($data);
    }
}
