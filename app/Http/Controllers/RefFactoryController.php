<?php

namespace App\Http\Controllers;

use App\Models\RefFactory;
use Exception;
use Illuminate\Http\Request;

class RefFactoryController extends Controller
{
    public function get(){
        try {
            $result['status'] = 200;
            $result['data'] = ['items' => RefFactory::all()];
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
