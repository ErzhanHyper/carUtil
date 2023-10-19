<?php

namespace App\Http\Controllers;

use App\Models\FileType;
use Illuminate\Http\Request;

class FileTypeController extends Controller
{
    public function get(){
        $data = FileType::all();
        return response()->json($data);
    }
}
