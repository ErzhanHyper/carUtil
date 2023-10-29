<?php

namespace App\Http\Controllers;

use App\Models\FileType;
use App\Models\FileTypeAgro;
use Illuminate\Http\Request;

class FileTypeController extends Controller
{
    public function get(Request $request){
        $data = FileType::all();
        return response()->json($data);
    }

    public function getAgro(Request $request){
        $data = FileTypeAgro::all();
        return response()->json($data);
    }
}
