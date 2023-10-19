<?php

namespace App\Http\Controllers;

use App\Models\TransferDeal;
use App\Services\Transfer\TransferService;
use Illuminate\Http\Request;

class TransferDealController extends Controller
{

    public function get(Request $request)
    {
        $data = app(TransferService::class)->getCollectionDeal($request);
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = app(TransferService::class)->storeDeal($request);
        return response()->json($data);
    }

    /**
     * Display the specified resource.
     */
    public function accept(Request $request, $id)
    {
        $data = app(TransferService::class)->acceptDeal($id);
        return response()->json($data);
    }

    public function close(Request $request, $id)
    {
        $data = app(TransferService::class)->closeDeal($id);
        return response()->json($data);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransferDeal $transferDeal)
    {
        //
    }
}
