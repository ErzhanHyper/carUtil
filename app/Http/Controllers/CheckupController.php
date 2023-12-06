<?php

namespace App\Http\Controllers;

use App\Services\CheckupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckupController extends Controller
{
    public function getCertById(Request $request, $id): JsonResponse
    {
        return response()->json(app(CheckupService::class)->checkCertById($id));
    }

    public function downloadCertByOrderId(Request $request, $id)
    {
        return app(CheckupService::class)->downloadCertByOrderId($request, $id);
    }
}
