<?php

namespace App\Http\Controllers;

use App\Models\AgroFile;
use App\Models\Car;
use App\Models\CarFile;
use App\Models\File;
use App\Models\Order;
use App\Models\PreOrderCar;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FileController extends Controller
{

    public function storeOrderFile(Request $request)
    {

        $order_id = $request->order_id;
        $order = Order::find($order_id);
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
            $file->file_type_id = $request->file_type_id;
            $file->client_id = $order->client_id;
            $file->ext = $extension;
            $file->original_name = $original_name;
            $file->save();
        }

        return response()->json($file);
    }

    public function getOrderFile(Request $request)
    {
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $files = [];

        if ($order) {
            $files = File::where('order_id', $order->id)->get();
        }

        return response()->json($files);
    }

    public function storePreOrderFile(Request $request)
    {
        if($request->preorder_id) {
            $file = $request->file;
            $original_name = time() . '_' . $file->getClientOriginalName();
            $extension = $file->extension();
            $file->move(public_path('storage/uploads/preorder/files/' . $request->preorder_id), $original_name);

            $file = new CarFile();
            $file->preorder_id = $request->preorder_id;
            $file->file_type_id = $request->file_type_id;
            $file->client_id = $request->client_id;
            $file->original_name = $original_name;
            $file->extension = $extension;
            $file->created_at = time();

            $file->save();

            return response()->json($file);
        }
    }

    public function getPreOrderFile(Request $request)
    {
        $preorder_id = $request->preorder_id;
        $preorder = PreOrderCar::find($preorder_id);

        $files = [];

        if ($preorder) {
            if ($preorder->recycle_type === 1) {
                $files = CarFile::where('preorder_id', $preorder_id)->get();
            } else if ($preorder->recycle_type === 2) {
                $files = AgroFile::where('preorder_id', $preorder_id)->get();
            }
        }

        return response()->json($files);
    }

    public function deletePreOrderFile(Request $request)
    {
        $preorder_id = $request->preorder_id;
        $file_id = $request->file_id;

        $preorder = PreOrderCar::find($preorder_id);


        if ($preorder->recycle_type === 1) {
            CarFile::where('preorder_id', $preorder_id)->where('id', $file_id)->delete();
        } else if ($preorder->recycle_type === 2) {
            AgroFile::where('preorder_id', $preorder_id)->where('id', $file_id)->delete();
        }

        return response()->json(['status', 'deleted']);
    }

    public function deleteOrderFile(Request $request)
    {
        $order_id = $request->order_id;
        $file_id = $request->file_id;
        $order = Order::find($order_id);
        if ($order) {
            File::where('order_id', $order->id)->where('id', $file_id)->delete();
        }

        return response()->json(['status', 'deleted']);
    }

}
