<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\File;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderFileController extends Controller
{
    public function uploadVideo(Request $request): \Illuminate\Http\JsonResponse
    {
        $order_id = $request->order_id;

        $validator = Validator::make($request->all(),
            [
                'file' => 'required|mimes:mp4|max:512000',
                'order_id' => 'required'
            ],
        );

        if ($validator->fails()) {
            return response()->json($validator->messages(), 400);
        }

        $message = 'Не загружена';
        $success = false;
        $upload = false;
        $status = 200;
        $order = Order::find($order_id);
        if ($order) {
            if ($order->approve === 0 || $order->approve === 1 || $order->approve === 2) {
                $message = 'Заявка не одобрена';
            } else if ($order->approve === 4) {
                $message = 'Невозможно загрузить видеозапись! Заявка отклонена';
            }
            if ($order->approve === 3 && $order->status === 3) {
                $message = 'Невозможно загрузить видеозапись! Сертификат уже выдан';
            }
            $car = Car::where('order_id', $order->id)->first();
            if ($order && $car && $order->approve === 3 && $order->status === 2) {
                $upload = true;
            }
        } else {
            $message = 'Заявка не найдена в базе';
        }

        if($upload) {
            $file = $request->file;
            $original_name = time() . '_' . $file->getClientOriginalName();
            $extension = $file->extension();
            $file->move(public_path('storage/uploads/order/files/' . $order->id), $original_name);

            $file = new File();
            $file->order_id = $order->id;
            $file->car_id = $car->id;
            $file->file_type_id = $car->id;
            $file->file_type_id = 29;
            $file->client_id = $order->client_id;
            $file->ext = $extension;
            $file->original_name = $original_name;
            $file->save();

            $message = 'Видеозапись успешно загружена';
            $success = true;
        }

        return response()->json([
            'message' => $message,
            'success' => $success
        ], $status);
    }
}
