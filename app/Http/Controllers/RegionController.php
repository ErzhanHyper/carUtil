<?php

namespace App\Http\Controllers;

use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function get(Request $request): JsonResponse
    {
        return response()->json(Region::all());
    }
}
