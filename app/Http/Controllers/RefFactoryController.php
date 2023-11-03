<?php

namespace App\Http\Controllers;

use App\Http\Resources\RefFactoryResource;
use App\Models\RefFactory;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RefFactoryController extends Controller
{
    public function get(){
        try {
            $paginate = 15;
            $pages = round(RefFactory::count() / $paginate);
            if ($pages == 0) {
                $pages = 1;
            }
            $result['status'] = 200;
            $result['data']['pages'] = $pages;
            $result['data']['items'] = RefFactoryResource::collection(RefFactory::orderBy('brand')->paginate($paginate));
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
                'factory' => 'required',
                'brand' => 'required',
                'model' => 'required',
                'category' => 'required',
                'class' => 'required',
            ]);
            if ($validator->fails()) {
                $result['data']['message'] = $validator->messages();
            }else {
                $data = new RefFactory();
                $data->factory = $request->factory;
                $data->brand = $request->brand;
                $data->model = $request->model;
                $data->category = $request->category;
                $data->class = $request->class;
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
            $result['data'] = RefFactory::find($id);
        } catch (Exception $e) {
            $result['status'] = 500;
            $result['data'] = ['message' => $e->getMessage()];
        }
        return response()->json($result['data'], $result['status']);
    }

    public function delete($id){
        try {
            $factory = RefFactory::find($id);
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
            $factory = RefFactory::find($id);
            if($request->factory) {
                $factory->factory = $request->factory;
            }
            if($request->brand) {
                $factory->brand = $request->brand;
            }
            if($request->model) {
                $factory->model = $request->model;
            }
            if($request->category) {
                $factory->category = $request->category;
            }
            if($request->class) {
                $factory->class = $request->class;
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
