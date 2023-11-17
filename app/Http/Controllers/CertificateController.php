<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Services\Certificate\CertificateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function get(Request $request): JsonResponse
    {
        $data = app(CertificateService::class)->getCollection($request);
        return response()->json($data);
    }

    public function generate(Request $request, $id)
    {
        return app(CertificateService::class)->generateCert($request, $id);
    }
}
