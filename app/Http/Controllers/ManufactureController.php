<?php

namespace App\Http\Controllers;

use App\Models\Manufacture;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ManufactureController extends Controller
{

    public function get(){
        try {
            $result['status'] = 200;
            $result['data'] = ['items' => Manufacture::all()];
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
            ]);
            if ($validator->fails()) {
                $result['data']['message'] = $validator->messages();
            }else {
                $data = new Manufacture();
                $data->title = $request->title;
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
            $result['data'] = Manufacture::find($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function delete($id){
        try {
            $factory = Manufacture::find($id);
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
            $factory = Manufacture::find($id);
            if($request->title) {
                $factory->title = $request->title;
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
