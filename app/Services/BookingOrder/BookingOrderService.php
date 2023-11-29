<?php


namespace App\Services\BookingOrder;


use App\Models\BookingOrder;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use InvalidArgumentException;

class BookingOrderService
{
    public function store($request)
    {

        $user = app(AuthService::class)->auth();
        $datetime = strtotime($request->datetime);
        $factory_id = $request->factory_id;

        if (isset($request->preorder_id)) {
            $preorder = PreOrderCar::find($request->preorder_id);
            if ($preorder && $preorder->liner_id === $user->id) {

                if ($preorder->order && $preorder->order->status === 3 && $preorder->order->approve === 3) {
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, заявка уже была обработана']]));
                }

                if ($preorder->status === 3) {
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, заявка была отклонена']]));
                }

                $closedDate = strtotime(date('d.m.Y', time()) . ' + 15 days');

                if ($datetime >= $closedDate) {
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, дата не больше 15 дней']]));
                }

                if ($datetime < time()) {
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, дата уже прошла']]));
                }

                $closedDate2 = strtotime(date('d.m.Y', $preorder->date) . ' + 15 days');

                $diffDate = $closedDate2 - time();

                if ($datetime > $closedDate2) {
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, дата не больше ' . date('j', $diffDate) . ' дней']]));
                }

                $data = BookingOrder::where('factory_id', $factory_id)->where('datetime', $datetime)->first();

                if (!$data) {
                    $booking_old = BookingOrder::find($preorder->booking_id);
                    if ($booking_old) {
                        $booking_old->delete();
                    }
                    $data = new BookingOrder();
                    $data->factory_id = $factory_id;
                    $data->datetime = $datetime;
                    $data->reserve = 1;

                    if ($data->save()) {
                        $preorder->booking_id = $data->id;
                        $preorder->factory_id = $factory_id;
                        $preorder->save();
                    }

                } else {
                    if ($preorder->booking_id !== $data->id) {
                        throw new InvalidArgumentException(json_encode(['booking' => ['Данное время уже забронирована']]));
                    }
                }
                return $data;
            }

        }
    }


}
