<?php


namespace App\Services\BookingOrder;


use App\Models\BookingOrder;
use App\Models\PreOrderCar;
use App\Services\AuthService;
use InvalidArgumentException;

class BookingOrderService
{

    public function get($request)
    {
        $data = BookingOrder::where('factory_id', $request->factory)->get();

        $datetime = [];

        foreach ($data as $item) {
            $datetime[] = [
                'id' => $item->id,
                'datetime' => date('Y-m-d h:i', $item->datetime),
            ];
        }

        return $datetime;
    }

    public function store($request)
    {

        $user = app(AuthService::class)->auth();
        $datetime = strtotime($request->datetime);

        $date = strtotime(date('d.m.Y', $datetime) . ' 00:00:00');

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

                $closedDate = strtotime(date('d.m.Y', $preorder->date) . ' + 15 days');

                if ($date > $closedDate) {
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, дата не больше 15 дней']]));
                }

                if ($date < time()) {
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, выберите дату со следующего дня']]));
                }

                $closedDate2 = strtotime(date('d.m.Y', $preorder->date) . ' + 15 days');

                $diffDate = $closedDate2 - time();

                if ($date > $closedDate2) {
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, дата не больше ' . date('j', $diffDate) . ' дней']]));
                }

                if(date('G', $datetime) > 18 ||  date('G', $datetime) < 9){
                    throw new InvalidArgumentException(json_encode(['booking' => ['Бронь недоступен, время не выбрана']]));
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

    public function delete($request)
    {
        $booking = BookingOrder::find($request->id);
        $preorder = PreOrderCar::find($request->preorder_id);
        $user = app(AuthService::class)->auth();
        $success = false;

        if($booking && $preorder){
            if($preorder->liner_id === $user->id) {
                $preorder->booking_id = null;
                $preorder->factory_id = null;
                if ($preorder->save()) {
                    $booking->delete();
                    $success = true;
                }
            }
        }
        return [
            'success' => $success
        ];
    }


}
