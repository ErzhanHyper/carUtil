<?php

namespace App\Http\Controllers;

use App\Services\Document\DocumentService;
use App\Services\Document\DocumentTransferService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function getStatement($id){

        return app(DocumentService::class)->generateStatement($id);
    }

    public function getContract($id){

        return app(DocumentService::class)->generateContract($id);
    }

    public function getExchangeApplication($id){

        return app(DocumentService::class)->generateExchangeApplication($id);
    }

    public function getTransferContract($id)
    {
        return app(DocumentTransferService::class)->generateTransferContract($id);
    }

}
