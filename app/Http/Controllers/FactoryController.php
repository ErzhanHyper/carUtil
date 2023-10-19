<?php

namespace App\Http\Controllers;

use App\Models\Factory;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    public function get(){
        $data = Factory::all();
        return response()->json($data);
    }
}
