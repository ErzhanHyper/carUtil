<?php


namespace App\Services\Sell;


use App\Models\Sell;
use App\Models\SellFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SellFileService
{

    public function store(Request $request)
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
        $sell = Sell::find($request->sell_id);

        if ($sell) {
            $file = $request->file;
            $original_name = time() . '_' . $file->getClientOriginalName();
            $extension = $file->extension();
            Storage::putFileAs('sell/files/'.$sell->id.'/', $file, $original_name);

            $exchangeFile = new SellFile();
            $exchangeFile->type = $request->type;
            $exchangeFile->sell_id = $sell->id;
            $exchangeFile->orig_name = $original_name;
            $exchangeFile->ext = $extension;
            $exchangeFile->save();
            $message = 'Файл успешно загружена';
            $success = true;
        }

        return [
            'success' => $success,
            'message' => $message
        ];
    }

    public function delete($id)
    {
        $sell = SellFile::find($id);
        if ($sell) {
            $sell->delete();
            return ['message' => 'Успешно удалена'];
        }
    }
}
