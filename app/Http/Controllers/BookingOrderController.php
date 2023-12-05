<?php

namespace App\Http\Controllers;

use App\Services\BookingOrder\BookingOrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingOrderController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        return response()->json(app(BookingOrderService::class)->get($request));
    }

    public function store(Request $request): JsonResponse
    {
        return response()->json(app(BookingOrderService::class)->store($request));
    }

    public function delete(Request $request): JsonResponse
    {
        return response()->json(app(BookingOrderService::class)->delete($request));
    }
}
