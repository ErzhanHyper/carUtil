<?php


namespace App\Services\Client;


use App\Models\Client;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class ClientService
{

    private function setData(array $data, object $client)
    {
        $client->title = $data['title'];
        $client->idnum = $data['idnum'];
        $client->client_type_id = $data['client_type_id'];
        $client->ud_num = $data['ud_num'];
        $client->ud_expired = $data['ud_expired'];
        $client->ud_issued_id = $data['ud_issued_id'];

        if(isset($data['address'])) {
            $client->address = $data['address'];
        }else{
            $client->address = '';
        }

        if(isset($data['phone'])) {
            $client->phone = $data['phone'];
        }else{
            $client->phone = '';
        }

        if(isset($data['email'])) {
            $client->email = $data['email'];
        }else{
            $client->email = '';
        }

        if(isset($data['region_id'])){
            $client->region_id = $data['region_id'];
        }

        if(isset($data['year'])){
            $client->year = $data['year'];
        }

        $client->save();
    }

    public function validateData($request){

        $validator = Validator::make($request, [
            'year' => 'required',
            'title' => 'required',
            'idnum' => 'required',
            'client_type_id' => 'required',
            'ud_num' => 'required',
            'ud_expired' => 'required',
            'ud_issued_id' => 'required',
            'address' => 'required',
            'phone' => 'required',
            'region_id' => 'required',
        ]);

        if ($validator->fails()) {
            throw new InvalidArgumentException($validator->messages());
        }
    }

    public function create($request)
    {
        $this->validateData($request);

        $client = new Client;
        $this->setData($request, $client);

        return $client;
    }


    public function update($request, $id)
    {
        $this->validateData($request);

        $client = Client::find($id);
        $this->setData($request, $client);

        return $client;
    }
}
