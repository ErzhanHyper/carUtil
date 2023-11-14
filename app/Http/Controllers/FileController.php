<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Models\AgroFile;
use App\Models\Car;
use App\Models\CarFile;
use App\Models\Client;
use App\Models\File;
use App\Models\Liner;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\TransferOrder;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PDF;
use InvalidArgumentException;

class FileController extends Controller
{

    public function generateOrderPFS($id)
    {
        $user = app(AuthService::class)->auth();
        $preorder = PreOrderCar::where('order_id', $id)->first();

        $transfer_order = TransferOrder::where('order_id', $preorder->order_id)->first();
        $owner_liner = Liner::where('id', $transfer_order->owner_liner_id)->first();
        $recipient_liner = Liner::where('id', $transfer_order->recipient_liner_id)->first();

        $owner_client = Client::where('idnum', $owner_liner->idnum)->with(['region', 'ud_issued'])->first();
        $recipient_client = Client::where('idnum', $recipient_liner->idnum)->first();

        $date = Carbon::parse($transfer_order->recipient_sign_time);
        $data = [
            'date' => $date->format('d') . '_' . $date->format('m') . '_' . $date->format('Y'),
            'city' => $owner_client->region->title,
            'owner_title' => $owner_client->title,
            'owner_ud_num' => $owner_client->ud_num,
            'owner_ud_expired' => $owner_client->ud_expired,
            'owner_ud_issued' => $owner_client->ud_issued->title,
            'recipient_title' => $recipient_client->title,
            'recipient_ud_num' => $recipient_client->ud_num,
            'recipient_ud_expired' => $recipient_client->ud_expired,
            'recipient_ud_issued' => $recipient_client->ud_issued->title
        ];

        if ($user) {
            if (($user->role === 'liner' && $user->id === $preorder->liner_id) || ($user->role === 'moderator') || $user->role === 'operator') {
                if ($preorder && $preorder->order_id) {
                    $folder = public_path('/storage/uploads/order/pfs');
                    if (!file_exists($folder)) {
                        mkdir($folder, 0777, true);
                    }
                    $pdf = PDF::loadView('templates.pfs', compact('data'));
                    $pdf->setPaper('a4', 'portrait')->setWarnings(false);
                    return $pdf->download('pfs.pdf');
                }
            }
        }
    }

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
            $file->created_at = time();
            $file->save();
        }

        return response()->json($file);
    }

    public function getOrderFile(Request $request)
    {
        $user = app(AuthService::class)->auth();

        if ($user) {
            $order_id = $request->order_id;
            $order = Order::find($order_id);
            $files = [];

            if ($order) {
                $files = FileResource::collection(File::where('order_id', $order->id)->get());
            }

            return response()->json($files);
        }
    }

    public function storePreOrderFile(Request $request)
    {

        $types = 'mimes:pdf,doc,docx,xlsx,jpeg,png,jpg,jfif';
        if (in_array($request->file_type_id, [8, 9, 10, 11, 12, 13, 14, 15, 16])) {
            $types = 'mimes:jpeg,png,jpg,jfif';
        }

        $validator = Validator::make($request->all(),
            [
                'file' => 'required|' . $types,
            ],
        );

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->messages());
        }

        if ($request->preorder_id) {
            $preorder = PreOrderCar::find($request->preorder_id);
            $file = $request->file;
            $original_name = time() . '_' . $file->getClientOriginalName();
            $extension = $file->extension();
            $file->move(public_path('storage/uploads/preorder/files/' . $request->preorder_id), $original_name);

            if ($preorder->recycle_type === 1) {
                $preorderFile = new CarFile();
            } else {
                $preorderFile = new AgroFile();
            }

            if ($preorderFile) {
                $preorderFile->preorder_id = $request->preorder_id;
                $preorderFile->file_type_id = $request->file_type_id;
                $preorderFile->client_id = $request->client_id;
                $preorderFile->original_name = $original_name;
                $preorderFile->extension = $extension;
                $preorderFile->created_at = time();

                $preorderFile->save();
            }

            return response()->json($preorderFile);
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
        $message = ['status' => 'can`t delete'];

        if ($preorder && ($preorder->status === 0 || $preorder->status === 4)) {
            if ($preorder->recycle_type === 1) {
                CarFile::where('preorder_id', $preorder_id)->where('id', $file_id)->delete();
            } else if ($preorder->recycle_type === 2) {
                AgroFile::where('preorder_id', $preorder_id)->where('id', $file_id)->delete();
            }
            $message = ['status' => 'deleted'];
        }

        return response()->json($message);
    }

    public function deleteOrderFile($id)
    {
        $user = app(AuthService::class)->auth();
        $message = ['status' => 'can`t delete'];

        if($user->role === 'operator') {
            $file_id = $id;
            $file = File::find($id);
            if ($file) {
                $order = Order::find($file->order_id);
                if ($order && ($order->status === 4 || $order->status === 0)) {
                    File::where('order_id', $order->id)->where('id', $file_id)->delete();
                    $message = ['status' => 'deleted'];
                }
            }
        }

        return response()->json($message);
    }

//    public function downloadOrderFile(Request $request, $id)
//    {
//        $user = app(AuthService::class)->auth();
//
//        if($user) {
//            return Storage::download('uploads/order/files/'.$id.'/'.$request->filename);
//        }
//    }
}
