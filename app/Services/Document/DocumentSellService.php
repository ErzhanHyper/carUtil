<?php


namespace App\Services\Document;


use Barryvdh\DomPDF\Facade\Pdf;

class DocumentSellService
{
    public function generateSellApplication($id){
        $data = [];

        $pdf = PDF::loadView('templates.sell_app', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);
        return $pdf->stream('sell_app.pdf');
    }
}
