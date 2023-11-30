<?php

namespace App\Http\Controllers;

use App\Http\Resources\FactoryResource;
use App\Models\Factory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FactoryController extends Controller
{
    public function get(){
        try {
            $result['status'] = 200;
            $result['data'] = ['items' => FactoryResource::collection(Factory::all())];
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function store(Request $request){
        try {
            $result['data']['success'] = false;

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'address' => 'required'
            ]);
            if ($validator->fails()) {
                $result['data']['message'] = $validator->messages();
            }else {
                $data = new Factory();
                $data->title = $request->title;
                $data->address = $request->address;
                $data->region_id = $request->region_id;
                $data->save();
                $result['data'] = $data;
                $result['data']['success'] = true;
            }

            $result['status'] = 200;
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function getById($id){
        try {
            $result['status'] = 200;
            $result['data'] = new FactoryResource(Factory::find($id));
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function delete($id){
        try {
            $factory = Factory::find($id);
            $factory->delete();
            $result['status'] = 200;
            $result['data'] = ['message' => 'deleted'];
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function update(Request $request, $id){
        try {
            $factory = Factory::find($id);
            if($request->title) {
                $factory->title = $request->title;
            }
            if($request->address) {
                $factory->address = $request->address;
            }
            if($request->region) {
                $factory->region_id = $request->region_id;
            }
            $factory->save();
            $result['status'] = 200;
            $result['data'] = $factory;
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }
}
