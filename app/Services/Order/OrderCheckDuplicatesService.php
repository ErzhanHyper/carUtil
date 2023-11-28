<?php


namespace App\Services\Order;


use App\Models\Car;
use App\Models\Order;

class OrderCheckDuplicatesService
{
    public function run($id): array
    {
        $order = Order::find($id);
        $car = Car::where('order_id', $order->id)->first();
        $c_repeat = [];
        $vin = trim($car->vin);
        if (mb_strlen($vin) > 5) {
            $short_vin = mb_substr($vin, -5, 5);
            $t_car = Car::select(['order_id', 'vin', 'id'])->where('vin', 'like', '%' . $short_vin . '%')->where('id', '!=', $car->id)->get();
            $tca = array();
            foreach ($t_car as $nn => $tc) {
                if ($tc->vin) {
                    $tca[] = array('id' => $tc->id, 'order_id' => $tc->order_id, 'vin' => $tc->vin);
                }
            }
            $c_repeat[$vin] = $tca;
            unset($tca, $t_car);
        }

        $body_repeat = [];
        $body = trim($car->body_no);
        if (mb_strlen($body) > 5) {
            $short_body = mb_substr($body, -5, 5);
            $t_car = Car::select(['order_id', 'body_no', 'id'])->where('body_no', 'like', '%' . $short_body . '%')->where('id', '!=', $car->id)->get();
            $tca = array();
            foreach ($t_car as $nn => $tc) {
                if ($tc->body_no) {
                    $tca[] = array('id' => $tc->id, 'body_no' => $tc->body_no, 'order_id' => $tc->order_id,);
                }
            }
            $body_repeat[$body] = $tca;
            unset($tca, $t_car);
        }

        $chassis_repeat = [];
        $chassis = trim($car->chassis_no);
        if (mb_strlen($chassis) > 5) {
            $short_chassis = mb_substr($chassis, -5, 5);
            $t_car = Car::select(['order_id', 'chassis_no', 'id'])->where('chassis_no', 'like', '%' . $short_chassis . '%')->where('id', '!=', $car->id)->get();
            $tca = array();
            foreach ($t_car as $nn => $tc) {
                if ($tc->chassis_no) {
                    $tca[] = array('id' => $tc->id, 'chassis_no' => $tc->chassis_no, 'order_id' => $tc->order_id,);
                }
            }
            $chassis_repeat[$chassis] = $tca;
            unset($tca, $t_car);
        }

        $c_body_repeat = array();
        $vin = trim($car->vin);
        if (mb_strlen($vin) > 5) {
            $short_vin = mb_substr($vin, -5, 5);
            $t_car = Car::select(['order_id', 'body_no', 'id', 'vin'])->where('body_no', 'like', '%' . $short_vin . '%')->where('id', '!=', $car->id)->get();
            $tca = array();
            foreach ($t_car as $nn => $tc) {
                if ($tc->vin) {
                    $tca[] = array('id' => $tc->id, 'vin' => $tc->vin, 'order_id' => $tc->order_id,);
                }
            }
            $c_body_repeat[$vin] = $tca;
            unset($tca, $t_car);
        }

        $body_vin_repeat = array();
        $body = trim($car->body_no);
        if (mb_strlen($body) > 5) {
            $short_body = mb_substr($body, -5, 5);
            $t_car = Car::select(['order_id', 'body_no', 'id', 'vin'])->where('vin', 'like', '%' . $short_body . '%')->where('id', '!=', $car->id)->get();
            $tca = array();
            foreach ($t_car as $nn => $tc) {
                if ($tc->body_no) {
                    $tca[] = array('id' => $tc->id, 'body_no' => $tc->body_no, 'order_id' => $tc->order_id,);
                }
            }
            $body_vin_repeat[$body] = $tca;
            unset($tca, $t_car);
        }

        return [
            'duplicates1' => $c_repeat,
            'duplicates2' => $c_body_repeat,
            'body_duplicates1' => $body_repeat,
            'chassis_duplicates1' => $chassis_repeat,
            'body_duplicates2' => $body_vin_repeat,
        ];
    }
}
