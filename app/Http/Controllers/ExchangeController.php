<?php

namespace App\Http\Controllers;

use App\Http\Resources\ExchangeResource;
use App\Models\Certificate;
use App\Models\Exchange;
use App\Models\ExchangeFile;
use App\Services\AuthService;
use App\Services\Exchange\ExchangeApproveService;
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

    public function storeFile(Request $request)
    {

        $validator = Validator::make($request->all(),
            [
                'file' => 'required | mimes:pdf,jpg,png,jpeg,webp,heic | max:512000',
            ],
        );

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $exchange = Exchange::find($request->exchange_id);

        if ($exchange) {
            $file = $request->file;
            $original_name = time() . '_' . $file->getClientOriginalName();
            $extension = $file->extension();
            $file->move(public_path('storage / uploads / exchange / files / ' . $exchange->id), $original_name);

            $exchangeFile = new ExchangeFile;
            $exchangeFile->type = $request->type;
            $exchangeFile->exchange_id = $exchange->id;
            $exchangeFile->orig_name = $original_name;
            $exchangeFile->ext = $extension;
            $exchangeFile->save();

            return response()->json(['Успешно загружена']);
        }
    }

    public function getFile(Request $request)
    {
        $files = ExchangeFile::where('exchange_id', $request->exchange_id)->get();
        return response()->json($files);
    }

    public function deleteFile(Request $request)
    {
        $file = ExchangeFile::find($request->exchange_file_id);
        if ($file) {
            $file->delete();
        }
        return response()->json(['Удалено']);
    }

}
