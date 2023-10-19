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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Certificate $certificate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Certificate $certificate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Certificate $certificate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Certificate $certificate)
    {
        //
    }
}
