<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\File;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderFileController extends Controller
{
    public function uploadVideo(Request $request): \Illuminate\Http\JsonResponse
    {
        $order_id = $request->order_id;

        $message = 'Не загружена';
        $status = 400;

        if ($order_id) {
            $order = Order::find($order_id);
            if ($order) {
                $car = Car::where('order_id', $order->id)->first();

                $file = $request->file;
                $original_name = time() . '_' . $file->getClientOriginalName();
                $extension = $file->extension();
                $file->move(public_path('storage/uploads/order/files/' . $order->id), $original_name);

                if ($order && $car) {
                    $file = new File();
                    $file->order_id = $order->id;
                    $file->car_id = $car->id;
                    $file->file_type_id = $car->id;
                    $file->file_type_id = 29;
                    $file->client_id = $order->client_id;
                    $file->ext = $extension;
                    $file->original_name = $original_name;
                    if ($file->save()) {
                        $message = 'Видео успешно загружена';
                        $status = 200;
                    }
                }
            }else{
                $message = 'Заявка не найдена в базе';
                $status = 400;
            }
        }else{
            $message = 'Номер заявки не найдена';
            $status = 400;
        }

        return response()->json(['message' => $message], $status);
    }
}
