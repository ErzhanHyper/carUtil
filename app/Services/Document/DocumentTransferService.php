<?php


namespace App\Services\Document;


use App\Models\TransferOrder;
use App\Services\AuthService;
use Barryvdh\DomPDF\Facade\Pdf;

class DocumentTransferService
{
    public function generateTransferContract($id)
    {
        $auth = app(AuthService::class)->auth();

        $data = [];
        $order = TransferOrder::find($id);

        $data['city'] = '';

        $pdf = PDF::loadView('templates.transfer_contract', compact('data'));
        $pdf->setPaper('a4', 'portrait')->setWarnings(false);
        return $pdf->download('transfer_contract.pdf');
    }
}
