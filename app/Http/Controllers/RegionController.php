<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function get(Request $request)
    {
        $data = Region::all();
        return response()->json($data);
    }
}
