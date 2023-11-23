<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function get(Request $request)
    {
        if($request->type === 'car'){
            $data = Category::whereIn('id', [1,2,3,4,5,6])->get();
        }else if($request->type === 'agro'){
            $data = Category::whereIn('id', [7,8])->get();
        }else{
            $data = Category::all();
        }
        return response()->json(['items' => $data]);
    }

    public function getComplect(Request $request)
    {
        $data = app(CategoryService::class)->complect($request->category);
        return response()->json(['items' => $data]);
    }
}
