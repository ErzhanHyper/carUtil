<?php

namespace App\Http\Controllers;

use App\Services\Sell\SellFileService;
use App\Services\Sell\SellService;
use Exception;
use Illuminate\Http\Request;

class SellController extends Controller
{
    public function get(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(SellService::class)->getCollection($request->params);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getById($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(SellService::class)->get($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getFilesById($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(SellService::class)->files($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function store(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(SellService::class)->create($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function update(Request $request, $id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(SellService::class)->update($request, $id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function storeFile(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(SellFileService::class)->store($request);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function deleteFile($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(SellFileService::class)->delete($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }
}
