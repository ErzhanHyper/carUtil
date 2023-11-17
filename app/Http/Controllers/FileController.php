<?php

namespace App\Http\Controllers;

use App\Http\Resources\FileResource;
use App\Models\AgroFile;
use App\Models\Car;
use App\Models\CarFile;
use App\Models\Certificate;
use App\Models\Client;
use App\Models\Exchange;
use App\Models\ExchangeFile;
use App\Models\File;
use App\Models\Liner;
use App\Models\Order;
use App\Models\PreOrderCar;
use App\Models\TransferOrder;
use App\Services\AuthService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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

    public function storeOrderFile(Request $request)
    {

        $order_id = $request->order_id;
        $order = Order::find($order_id);
        $car = Car::where('order_id', $order->id)->first();

        $file = $request->file;
        $original_name = time() . '_' . $file->getClientOriginalName();
        $extension = $file->extension();
        $path = 'order/files/'.$order->id.'/';
        Storage::putFileAs($path, $file, trim($original_name));

        if ($order && $car) {
            $file = new File();
            $file->order_id = $order->id;
            $file->car_id = $car->id;
            $file->file_type_id = $car->id;
            $file->file_type_id = $request->file_type_id;
            $file->client_id = $order->client_id;
            $file->extension = $extension;
            $file->original_name = trim($original_name);
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

        $types = 'mimes:pdf,doc,docx,xlsx,jpeg,png,jpg,jfif,webp';
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

        $preorder = PreOrderCar::find($request->preorder_id);
        if ($preorder) {
            $file = $request->file;
            $original_name = time() . '_' . $file->getClientOriginalName();
            $extension = $file->extension();
            $path = 'preorder/files/'.$preorder->id.'/';

            Storage::putFileAs($path, $file, $original_name);

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
                $file = CarFile::where('preorder_id', $preorder_id)->where('id', $file_id)->first();
                if($file) {
                    $path = 'preorder/files/'.$preorder->id.'/'.$file->original_name;
                    if(Storage::exists($path)) {
                        Storage::delete($path);
                    }
                    $file->delete();
                }
            } else if ($preorder->recycle_type === 2) {
                $file = AgroFile::where('preorder_id', $preorder_id)->where('id', $file_id)->first();
                if($file) {
                    $path = 'preorder/files/'.$preorder->id.'/'.$file->original_name;
                    if(Storage::exists($path)) {
                        Storage::delete($path);
                    }
                    $file->delete();
                }
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
                    File::where('order_id', $order->id)->where('id', $file_id)->first();
                    if($file) {
                        $path = 'order/files/'.$order->id.'/'.$file->original_name;
                        if(Storage::exists($path)) {
                            Storage::delete($path);
                        }
                        $file->delete();
                    }
                    $message = ['status' => 'deleted'];
                }
            }
        }

        return response()->json($message);
    }

    public function getFile(Request $request, $id)
    {
        $user = app(AuthService::class)->auth();
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        if($order) {
            if ($user->role === 'operator' || $user->role === 'moderator') {
                $file = File::find($id);
                $filePath = '/order/files/' . $order->id . '/' . $file->original_name;
                $path = Storage::disk('local')->path($filePath);
                header('Content-Disposition: inline; filename="' . $file->original_name . '');

                return response()->file($path);

            }
        }
    }

    public function getImage(Request $request, $id){
        $user = app(AuthService::class)->auth();
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        if($order) {
            if ($user->role === 'operator' || $user->role === 'moderator') {
                $file = File::find($id);
                $filePath = '/order/files/' . $order->id . '/' . $file->original_name;
                $path = Storage::disk('local')->get($filePath);
                return base64_encode($path);
            }
        }
    }

    public function getVideo(Request $request, $id){
        $user = app(AuthService::class)->auth();
        $order_id = $request->order_id;
        $order = Order::find($order_id);
        if($order) {
            if ($user->role === 'operator' || $user->role === 'moderator') {
                $file = File::find($id);
                $filePath = '/order/files/' . $order->id . '/' . $file->original_name;
                $path = Storage::disk('local')->get($filePath);
                return base64_encode($path);
            }
        }
    }

    public function getCarFile(Request $request, $id)
    {
        $user = app(AuthService::class)->auth();
        $preorder_id = $request->preorder_id;
        $preorder = PreOrderCar::find($preorder_id);
        $file = CarFile::find($id);
        $filePath = '/preorder/files/'.$preorder_id.'/'.$file->original_name;
        $path = Storage::disk('local')->path($filePath);

        if($preorder){

            if($user->role === 'moderator' || ($user->role === 'liner' && $user->id === $preorder->liner_id)){
                if ($file->extension == 'jpg' || $file->extension == 'JPG' || $file->extension == 'jpeg' || $file->extension == 'JPEG' || $file->extension == 'jfif' || $file->extension == 'JFIF') {
                    $ct = 'image/jpeg';
                } else if ($file->extension == 'png' || $file->extension == 'PNG') {
                    $ct = 'image/png';
                } else if ($file->extension == 'pdf' || $file->extension == 'PDF') {
                    $ct = 'application/pdf';
                } else if ($file->extension == 'rar' || $file->extension == 'RAR') {
                    $ct = 'application/rar';
                } else if ($file->extension == 'zip' || $file->extension == 'ZIP') {
                    $ct = 'application/zip';
                } else if ($file->extension == 'webm' || $file->extension == 'webm') {
                    $ct = 'video/webm';
                } else if ($file->extension == 'mp4' || $file->extension == 'mp4') {
                    $ct = 'video/mp4';
                }

                if (file_exists($path)) {
                    if (ob_get_level()) {
                        ob_end_clean();
                    }
                    header('Content-Type: ' . $ct);
                    header('Content-Disposition: inline; filename="' . $file->original_name . '');
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($path));
                    // читаем файл и отправляем его пользователю
                    readfile($path);
                    // не взумайте вставить сюда exit
                }
            }
        }
    }

    public function getAgroFile(Request $request, $id)
    {
        $user = app(AuthService::class)->auth();
        $preorder_id = $request->preorder_id;
        $preorder = PreOrderCar::find($preorder_id);
        $file = AgroFile::find($id);
        $filePath = '/preorder/files/'.$preorder_id.'/'.$file->original_name;
        $path = Storage::disk('local')->path($filePath);

        if($preorder){
            if($user->role === 'moderator' || ($user->role === 'liner' && $user->id === $preorder->liner_id)){
                if ($file->extension == 'jpg' || $file->extension == 'JPG' || $file->extension == 'jpeg' || $file->extension == 'JPEG' || $file->extension == 'jfif' || $file->extension == 'JFIF') {
                    $ct = 'image/jpeg';
                } else if ($file->extension == 'png' || $file->extension == 'PNG') {
                    $ct = 'image/png';
                } else if ($file->extension == 'pdf' || $file->extension == 'PDF') {
                    $ct = 'application/pdf';
                } else if ($file->extension == 'rar' || $file->extension == 'RAR') {
                    $ct = 'application/rar';
                } else if ($file->extension == 'zip' || $file->extension == 'ZIP') {
                    $ct = 'application/zip';
                } else if ($file->extension == 'webm' || $file->extension == 'webm') {
                    $ct = 'video/webm';
                } else if ($file->extension == 'mp4' || $file->extension == 'mp4') {
                    $ct = 'video/mp4';
                }

                if (file_exists($path)) {
                    if (ob_get_level()) {
                        ob_end_clean();
                    }
                    header('Content-Type: ' . $ct);
                    header('Content-Disposition: inline; filename="' . $file->original_name . '');
                    header('Content-Transfer-Encoding: binary');
                    header('Expires: 0');
                    header('Cache-Control: must-revalidate');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($path));
                    // читаем файл и отправляем его пользователю
                    readfile($path);
                    // не взумайте вставить сюда exit
                }
            }
        }
    }

    public function getCarFileImage(Request $request, $id){
        $user = app(AuthService::class)->auth();
        $preorder_id = $request->preorder_id;
        $preorder = PreOrderCar::find($preorder_id);
        $transfer_find = null;
        if($preorder) {
            $order = Order::find($preorder->order_id);
            if($order) {
                $transfer_find = TransferOrder::where('closed', '!=', 2)->where('order_id', $order->id)->first();
            }
            if ($user->role === 'moderator' || ($user->role === 'liner' && $user->id === $preorder->liner_id)  || $transfer_find) {
                $file = CarFile::find($id);
                $filePath = '/preorder/files/' . $preorder_id . '/' . $file->original_name;
                $path = Storage::disk('local')->get($filePath);

                return base64_encode($path);
            }
        }
    }

    public function getAgroFileImage(Request $request, $id){
        $user = app(AuthService::class)->auth();
        $preorder_id = $request->preorder_id;
        $preorder = PreOrderCar::find($preorder_id);
        $transfer_find = null;
        if($preorder) {
            $order = Order::find($preorder->order_id);
            if($order) {
                $transfer_find = TransferOrder::where('closed', '!=', 2)->where('order_id', $order->id)->first();
            }
            if ($user->role === 'moderator' || ($user->role === 'liner' && $user->id === $preorder->liner_id) || $transfer_find) {
                $file = AgroFile::find($id);
                $filePath = '/preorder/files/' . $preorder_id . '/' . $file->original_name;
                $path = Storage::disk('local')->get($filePath);

                return base64_encode($path);
            }
        }
    }

    public function downloadExchangeFile(Request $request, $id)
    {
        $user = app(AuthService::class)->auth();
        $exchange = Exchange::find($request->exchange_id);
        $can = false;
        if($exchange) {

            $cert = Certificate::find($exchange->certificate_id);
            if($user->role === 'liner') {
                if ($user->idnum === $cert->idnum_1 || $user->idnum === $exchange->idnum) {
                    $can = true;
                }
            }else if($user->role === 'moderator'){
                $can = true;
            }
            if($can) {
                $file = ExchangeFile::find($id);
                $filePath = '/exchange/files/' . $exchange->id . '/' . $file->orig_name;
                $path = Storage::disk('local')->path($filePath);
                header('Content-Disposition: inline; filename="' . $file->orig_name . '');

                return response()->file($path);
            }
        }
    }
}
