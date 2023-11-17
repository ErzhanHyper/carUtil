<?php


namespace App\Services\Exchange;


use App\Models\Certificate;
use App\Models\Exchange;
use App\Models\ExchangeFile;
use App\Services\AuthService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ExchangeFileService
{
    public function store($request, $id)
    {
        $validator = Validator::make($request->all(),
            [
                'file' => 'required | mimes:pdf,jpg,png,jpeg,webp,heic | max:512000',
            ],
        );
        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $success = false;
        $message = 'Нет доступа';
        $exchange = Exchange::find($id);

        if ($exchange) {
            $file = $request->file;
            $original_name = time() . '_' . $file->getClientOriginalName();
            $extension = $file->extension();
            Storage::putFileAs('exchange/files/'.$exchange->id.'/', $file, $original_name);

            $exchangeFile = new ExchangeFile;
            $exchangeFile->type = $request->type;
            $exchangeFile->exchange_id = $exchange->id;
            $exchangeFile->orig_name = $original_name;
            $exchangeFile->ext = $extension;
            $exchangeFile->save();
            $message = 'Файл успешно загружена';
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function get($id)
    {
        $user = app(AuthService::class)->auth();
        $can = false;

        if($user->role === 'moderator'){
            $can = true;
        }

        $files = ExchangeFile::where('exchange_id', $id)->get();
        $exchange = Exchange::find($id);
        if($exchange) {
            $cert = Certificate::find($exchange->certificate_id);
            if($user->role === 'liner'){
                if($user->idnum === $cert->idnum_1 || $user->idnum === $exchange->idnum){
                    $can = true;
                }
            }
        }

        if($can) {
            return $files;
        }
    }

    public function delete($id)
    {
        $user = app(AuthService::class)->auth();
        $file = ExchangeFile::find($id);
        if($file) {
            $can = false;
            $exchange = Exchange::find($file->exchange_id);
            if($exchange) {
                $cert = Certificate::find($exchange->certificate_id);
                if($user->role === 'liner'){
                    if($user->idnum === $cert->idnum_1){
                        $can = true;
                    }
                }
                if($can) {
                    $path = 'exchange/files/' . $exchange->id . '/' . $file->orig_name;
                    if (Storage::exists($path)) {
                        Storage::delete($path);
                    }
                    $file->delete();
                }
            }
            return [
                'success' => true,
                'message' => 'Удалено'
            ];
        }
    }
}
