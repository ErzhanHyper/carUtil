<?php


namespace App\Services\Car;


use App\Models\Car;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class CarService
{
    private function setData($data, object $car)
    {
        $car->vin = strtoupper($data->vin);
        $car->grnz = $data->grnz;
        $car->category_id = $data->category_id;
        $car->year = $data->year;
        $car->m_model = $data->m_model;
        $car->body_no = $data->body_no;
        $car->chassis_no = $data->chassis_no;
        $car->weight = $data->weight;

        if(isset($data->color) && $data->color != '') {
            $car->color = $data->color;
        }

        if(isset($data->engine_no) && $data->engine_no != '') {
            $car->engine_no = $data->engine_no;
        }

        $car->cert_idnum = $data->cert_idnum;
        $car->cert_title = $data->cert_title;

        $car->owner_title = $data->owner_title;
        $car->owner_idnum = $data->owner_idnum;

        if(isset($data->doors_count) && $data->doors_count != ''){
            $car->doors_count = $data->doors_count;
        }else{
            $car->doors_count = 0;
        }

        if(isset($data->wheels_count) && $data->wheels_count != ''){
            $car->wheels_count = $data->wheels_count;
        }else{
            $car->wheels_count = 0;
        }

        if(isset($data->wheels_protector_count) && $data->wheels_protector_count != ''){
            $car->wheels_protector_count = $data->wheels_protector_count;
        }else{
            $car->wheels_protector_count = 0;
        }

        if(isset($data->proxy_num)) {
            $car->proxy_num = $data->proxy_num;
        }

        if(isset($data->proxy_date)) {
            $car->proxy_date = $data->proxy_date;
        }

        $car->save();
    }

    public function validateData($request){

        $validator = Validator::make($request->all(), [
            'vin' => 'required',
            'grnz' => 'required',
            'category_id' => 'required',
            'year' => 'required',
            'body_no' => 'required',
            'chassis_no' => 'required',
            'weight' => 'required',
            'm_model' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->messages());
        }
    }

    public function create($request)
    {
        $this->validateData($request);

        $car = new Car();
        $this->setData($request, $car);

        return $car;
    }


    public function update($request, $id)
    {
        $this->validateData($request);

        $car = Car::find($id);
        $this->setData($request, $car);

        return $car;
    }
}
