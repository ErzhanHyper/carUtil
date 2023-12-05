<?php

namespace App\Http\Controllers;

use App\Services\Certificate\CertificateService;
use App\Services\CheckupService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get(Request $request): JsonResponse
    {
        return response()->json(app(CertificateService::class)->getCollection($request));
    }

    public function checkById(Request $request, $id): JsonResponse
    {
        return response()->json(app(CheckupService::class)->checkCertById($id));
    }
}
