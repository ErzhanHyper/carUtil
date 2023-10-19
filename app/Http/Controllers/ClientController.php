<?php

namespace App\Http\Controllers;

use App\Models\Bank;
use App\Models\Client;
use App\Models\Region;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function get()
    {
        $data = Client::with('region')->paginate(10);
        return Inertia::render('client/Client', ['data' => $data]);
    }
//
//    public function getById($id)
//    {
//        $banks = Bank::all();
//        $region = Region::all();
//
//        $data = Client::find($id);
//        $data->region;
//
//        return Inertia::get([
//            'data' => $data,
//            'banks' => $banks,
//            'region' => $region
//        ]);
//    }

    public function showById($id)
    {
        $banks = Bank::all();
        $region = Region::all();

        $data = Client::find($id);
        $data->region;

        return Inertia::render('client/ClientDetail', [
            'data' => $data,
            'banks' => $banks,
            'region' => $region
        ]);
    }
}
