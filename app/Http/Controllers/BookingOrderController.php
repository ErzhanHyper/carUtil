<?php

namespace App\Http\Controllers;

use App\Models\BookingOrder;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use App\Services\BookingOrder\BookingOrderService;
use App\Services\Preorder\PreorderService;
use Illuminate\Http\Request;

class BookingOrderController extends Controller
{
    public function get(Request $request)
    {

        $data = BookingOrder::where('factory_id', $request->factory)->get();

        $datetime = [];

        foreach ($data as $item) {
            $datetime[] = [
                'id' => $item->id,
                'datetime' => date('Y-m-d h:i', $item->datetime),
            ];
        }

        return response()->json($datetime);
    }

    public function store(Request $request)
    {
        $data = app(BookingOrderService::class)->store($request);
        return response()->json($data);
    }

    public function delete(Request $request){
        $booking = BookingOrder::find($request->id);
        $preorder = PreOrderCar::find($request->preorder_id);
        $user = app(AuthService::class)->auth();

        if($booking && $preorder){
            if($preorder->liner_id === $user->id) {
                $preorder->booking_id = null;
                $preorder->factory_id = null;
                if ($preorder->save()) {
                    $booking->delete();
                    return ['message' => 'deleted'];
                }
            }
        }
    }

}
