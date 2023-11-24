<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExchangeResource;
use App\Models\Certificate;
use App\Models\Exchange;
use App\Models\ExchangeFile;
use App\Services\AuthService;
use App\Services\Exchange\ExchangeApproveService;
use App\Services\Exchange\ExchangeFileService;
use App\Services\Exchange\ExchangeService;
use App\Services\SignService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExchangeController extends Controller
{

    public function get(Request $request)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(ExchangeService::class)->getCollection($request->params);
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
            $result['data'] = app(ExchangeService::class)->getById($id);
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
            $result['data'] = app(ExchangeService::class)->create($request);
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
            $result['data'] = app(ExchangeService::class)->update($request, $id);
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
            $result['data'] = app(ExchangeService::class)->delete($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function approve($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(ExchangeApproveService::class)->approve($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function decline($id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(ExchangeApproveService::class)->decline($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function message(Request $request, $id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(ExchangeApproveService::class)->message($request, $id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function storeFile(Request $request, $id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(ExchangeFileService::class)->store($request, $id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getFile(Request $request, $id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(ExchangeFileService::class)->get($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function deleteFile(Request $request,$id)
    {
        try {
            $result['status'] = 200;
            $result['data'] = app(ExchangeFileService::class)->delete($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

}
