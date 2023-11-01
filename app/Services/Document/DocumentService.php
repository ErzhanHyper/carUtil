<?php


namespace App\Services\Document;


use Barryvdh\DomPDF\Facade\Pdf;

class DocumentService
{

    public function generateStatement($id){
        $data = [];
        $pdf = PDF::loadView('templates.statement', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);

        return $pdf->download('statement.pdf');
    }


    public function generateExchangeApplication($id){
        $data = [];
        $pdf = PDF::loadView('templates.exchange_app', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);

        return $pdf->download('exchange_app.pdf');
    }
}
