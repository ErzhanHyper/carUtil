<?php

namespace App\Http\Controllers;

use App\Models\Manufactory;
use Illuminate\Http\Request;

class ManufactoryController extends Controller
{

    public function get()
    {
        $data = Manufactory::all();
        return response()->json($data);
    }

}
