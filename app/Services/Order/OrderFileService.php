<?php


namespace App\Services\Order;


use App\Models\File;

class OrderFileService
{
    public function store($request): void
    {
        $file = new File();
        $file->order_id = $request->order_id;
        $file->car_id = $request->car_id;
        $file->file_type_id = $request->file_type_id;
        $file->client_id = $request->client_id;
        $file->extension = $request->extension;
        $file->original_name = $request->original_name;
        $file->created_at = time();
        $file->save();
    }
}
