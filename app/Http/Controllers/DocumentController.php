<?php

namespace App\Http\Controllers;

use App\Services\Document\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function getStatement($id){

        return app(DocumentService::class)->generateStatement($id);
    }

    public function getExchangeApplication($id){

        return app(DocumentService::class)->generateExchangeApplication($id);
    }

}
