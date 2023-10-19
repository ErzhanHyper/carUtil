<?php

namespace App\Http\Controllers;

use App\Models\BookingOrder;
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



}
