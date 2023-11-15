<?php

namespace App\Http\Controllers;

use App\Models\TransferDeal;
use App\Services\Transfer\TransferDealService;
use App\Services\Transfer\TransferService;
use Exception;
use Illuminate\Http\Request;

class TransferDealController extends Controller
{

    public function get(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferDealService::class)->getCollection($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferDealService::class)->store($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    /**
     * Display the specified resource.
     */
    public function accept($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferDealService::class)->accept($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function close($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(TransferDealService::class)->close($id);
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
            $result['data'] = app(TransferDealService::class)->delete($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TransferDeal $transferDeal)
    {
        //
    }
}
