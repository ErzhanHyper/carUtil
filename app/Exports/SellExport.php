<?php

namespace App\Exports;

use App\Models\Car;
use App\Models\Category;
use App\Models\Certificate;
use App\Models\Client;
use App\Models\Order;
use App\Models\RefFactory;
use App\Models\Region;
use App\Models\Sell;
use App\Models\User;
use App\Services\AuthService;
use App\Services\Certificate\CalcCertificatePriceService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SellExport implements  FromCollection, WithMapping, WithHeadings, WithHeadingRow, WithEvents
{
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }


    public function collection()
    {
        $start = strtotime(date("d.m.Y 00:00:00", strtotime($this->data->start)));
        $end = strtotime(date("d.m.Y 23:59:59", strtotime($this->data->end)));

        $auth = app(AuthService::class)->auth();

        if($auth->role === 'moderator') {
            $sell = Sell::whereBetween('closed', [$start, $end])->get();
        }else if($auth->role === 'dealer-chief') {
            $sells = Sell::select(['id', 'user_id', 'closed'])->whereBetween('closed', [$start, $end])->get();
            $sell_ids = [];
            foreach ($sells as $sell) {
                $user = User::find($sell->user_id);
                if ($user->custom_2 === $auth->custom_2) {
                    $sell_ids[] = $sell->id;
                }
            }
            $sell = Sell::whereIn('id', $sell_ids)->get();
        }
        return $sell;
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Style the first row as bold text.
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            '№ договора',
            'Сертификат 1',
            'Дата 1',
            'Категория 1',
            'Сумма 1',

            'Сертификат 2',
            'Дата 2',
            'Категория 2',
            'Сумма 2',

            'Сертификат 3',
            'Дата 3',
            'Категория 3',
            'Сумма 3',

            'Сертификат 4',
            'Дата 4',
            'Категория 4',
            'Сумма 4',

            'Сумма скидки',
            'Категория ТС',
            'Контрагент',
            'ИИН / БИН контрагента',
            'ФЛ / ЮЛ',
            'Регион погашения',
            'VIN выданного ТС',
            'Год выпуска',

            'ФИО дилера',
            'Дилерский центр',
            'Марка',
            'Модель',
            'Производитель',
            'Дата создания погашения',
            'Дата погашения',
        ];
    }

    public function map($row): array
    {
        $cert1 = Certificate::find($row->cert_1);
        $car1 = Car::find($cert1->car_id);
        $sum1 = app(CalcCertificatePriceService::class)->run($cert1->id);
        $order = Order::find($car1->order_id);
        $client_type = '';
        if($order){

            $client = Client::find($order->client_id);
            if($client){
                if($client->client_type_id === 1){
                    $client_type = 'ФЛ';
                }else{
                    $client_type = 'ЮЛ';
                }
            }
        }

        $user = User::find($row->user_id);
        $region = Region::find($user->custom_3);

        $refFactory = RefFactory::find($row->subject);
        $category = Category::find($refFactory->category);
        $cert2 = null;
        $car2 = null;
        $sum2 = null;
        $idnum1 = $cert1->idnum_1;
        $title1 = $cert1->title_1;
        if($row->cert_2) {
            $cert2 = Certificate::find($row->cert_2);
            if($cert2) {
                $car2 = Car::find($cert2->car_id);
                $sum2 = app(CalcCertificatePriceService::class)->run($cert2->id);
            }
        }

        $cert3 = null;
        $car3 = null;
        $sum3 = null;
        if($row->cert_3) {
            $cert3 = Certificate::find($row->cert_3);
            if($cert3) {
                $car3 = Car::find($cert3->car_id);
                $sum3 = app(CalcCertificatePriceService::class)->run($cert3->id);
            }
        }

        $cert4 = null;
        $car4 = null;
        $sum4 = null;
        if($row->cert_4) {
            $cert4 = Certificate::find($row->cert_4);
            if($cert4) {
                $car4 = Car::find($cert4->car_id);
                $sum4 = app(CalcCertificatePriceService::class)->run($cert4->id);
            }
        }

       return [
           $row->id,
           $cert1->id,
           date('d.m.Y', $cert1->date_1),
           $car1->category->title,
           $sum1['sum'],

           $cert2 ? $cert2->id : '-',
           $cert2 ? date('d.m.Y', $cert2->date_1) : '-',
           $car2 ? $car2->category->title : '-',
           $sum2 ? $sum2['sum'] : '-',

           $cert3 ? $cert3->id : '-',
           $cert3 ? date('d.m.Y', $cert3->date_1) : '-',
           $car3 ? $car3->category->title : '-',
           $sum3 ? $sum3['sum'] : '-',

           $cert4 ? $cert4->id : '-',
           $cert4 ? date('d.m.Y', $cert4->date_1) : '-',
           $car4 ? $car4->category->title : '-',
           $sum4 ? $sum4['sum'] : '-',

           $row->sum,
           $category ? $category->title_ru : '-',
           $title1,
           $idnum1.' ',
           $client_type,
           $region ? $region->title : '-',
           $row->vin,
           $row->year,

           $user->title,
           $user->custom_1,
           $refFactory->brand,
           $refFactory->model,
           $refFactory->factory,
           date('d.m.Y', $refFactory->created),
           date('d.m.Y', $row->closed)
       ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $columns = ["A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", "AA", "AB", "AC", "AD", "AE", "AF"];
                foreach ($columns as $column) {

                    $event->sheet->getStyle($column)->getAlignment()->setHorizontal('left');

                    $event->sheet->getDelegate()->getStyle($column)->getFont()->setSize(12);

                    $event->sheet->getDelegate()->getStyle('A1:AF1')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFCC99');

                    $event->sheet->getDelegate()->getStyle('A1:AF1')
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $event->sheet->getColumnDimension($column);
                    $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(14);
                    $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(15);
                    $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(40);
                    $event->sheet->getDelegate()->getColumnDimension('M')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('N')->setWidth(14);
                    $event->sheet->getDelegate()->getColumnDimension('O')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('P')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('Q')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('R')->setWidth(40);
                    $event->sheet->getDelegate()->getColumnDimension('S')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('T')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('U')->setWidth(10);
                    $event->sheet->getDelegate()->getColumnDimension('V')->setWidth(30);
                    $event->sheet->getDelegate()->getColumnDimension('W')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('X')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('Y')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('Z')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('AA')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('AB')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('AC')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('AD')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('AE')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('AF')->setWidth(25);
                }
            }
        ];
    }
}
