<?php


namespace App\Services\BookingOrder;


use App\Models\BookingOrder;
use App\Models\PreOrderCar;
use InvalidArgumentException;

class BookingOrderService
{
    public function store($request)
    {
        $datetime = strtotime($request->datetime);
        $factory_id = $request->factory_id;

        $data = BookingOrder::where('factory_id', $factory_id)->where('datetime', $datetime)->first();
        if(!$data) {
            $data = new BookingOrder();
            $data->factory_id = $factory_id;
            $data->datetime = $datetime;
            $data->reserve = 1;
            $data->save();
        }else{
            if(isset($request->preorder_id)){
                $preorder = PreOrderCar::find($request->preorder_id);
                if($preorder->booking_id !== $data->id){
                    throw new InvalidArgumentException(json_encode(['booking' => ['Данное время уже забронирована']]));
                }
            }
        }

        return $data;
    }

}
