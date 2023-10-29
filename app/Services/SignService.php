<?php


namespace App\Services;


use App\Models\Car;

class SignService
{
    public function __signData($id){
        $car = Car::find($id);
        $hashed_string = 'id:'. $car->id .' vin:'. $car->vin .' grnz:'. $car->grnz.' chassis_no:'. $car->chassis_no;
        $hashed_string .= ' body_no:'. $car->body_no .' year:'. $car->year .' category_id:'. $car->category_id;
        $hashed_string .= ' car_type_id:'. $car->car_type_id .' order_id:'. $car->order_id .' proxy:'. $car->proxy;
        $hashed_string .= ' order_type:'. $car->order_type .' cert_title:'. $car->cert_title;
        $hashed_string .= ' cert_idnum:'. $car->cert_idnum .' owner_type:'. $car->owner_type;
        $zipped_string = gzencode($hashed_string);
        $zipped_string = base64_encode($zipped_string);

        $car->hash = $zipped_string;
        $car->save();

        return $zipped_string;
    }
}
