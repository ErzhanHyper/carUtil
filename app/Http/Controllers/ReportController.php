<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ReportController extends Controller
{

    public function index(){
        return Inertia::render('report/Report', []);
    }

    public function certificate(){

    }
}
