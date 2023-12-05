<?php

namespace App\Http\Controllers;

use App\Services\Document\DocumentCertService;
use App\Services\Document\DocumentSellService;
use App\Services\Document\DocumentService;
use App\Services\Document\DocumentTransferService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function getStatement($id)
    {
        return app(DocumentService::class)->generateStatement($id);
    }

    public function getContract($id)
    {
        return app(DocumentService::class)->generateContract($id);
    }

    public function getExchangeApplication($id)
    {
        return app(DocumentService::class)->generateExchangeApplication($id);
    }

    public function getTransferContract(Request $request, $id)
    {
        return app(DocumentTransferService::class)->generateTransferContract($request, $id);
    }

    public function getSellApplication($id)
    {
        return app(DocumentSellService::class)->generateSellApplication($id);
    }

    public function getComplect(Request $request, $id)
    {
        return app(DocumentService::class)->generateComplect($request, $id);
    }

    public function getKapReference(Request $request, $id)
    {
        return app(DocumentService::class)->generateKapReference($request, $id);
    }

    public function getCertificate(Request $request, $id)
    {
        return app(DocumentCertService::class)->generateCert($request, $id);
    }
}
