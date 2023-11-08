<?php

namespace App\Exports;

use App\Models\Car;
use App\Models\Certificate;
use App\Services\Certificate\CalcCertificatePriceService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CertExport implements FromCollection, WithMapping, WithHeadings, WithHeadingRow, WithEvents
{
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
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
            'Номер договора',
            'Номер сертификата',
            'Дата сертификата',
            'Сумма скидки',
            'Категория ТС',
            'Контрагент',
            'ИИН/БИН Контрагента',
            'ФЛ/ЮЛ',
            'Регион выдачи',
            'Заблокирована',
            'Погашено',
            'Дата истечения'
        ];
    }

    public function map($row): array
    {
        $car = Car::find($row->car_id);
        $category = '';
        $client_type = '';
        $region = '';
        $blocked = $row->blocked === 1 ? 'Да' : 'Нет';
        $closed = $row->closed === 1 ? 'Да' : 'Нет';

        $new_date = 1705514400 - $row[9];
        if( $row[9] >= 1610992800 && $row[9] <= 1642442400){
            $fdate = date('d.m.Y', $row->date + $new_date);
        }else{
            $fdate = date('d.m.Y', $row->date+(364*24*3600));
        }

        if($car) {
            $category = $car->category->title;
        }

        $sum = app(CalcCertificatePriceService::class)->run($row->id);

        return [
            $car ? $car->order_id : '-',
            $row->id,
            date('d.m.Y', $row->date),
            $sum['sum'],
            $category,
            $row->title_1,
            strval($row->idnum_1) . ' ',
            $client_type,
            $region,
            $blocked,
            $closed,
            $fdate
        ];
    }

    public function collection()
    {
        $start = strtotime(date("d.m.Y 00:00:00", strtotime($this->data->start)));
        $end = strtotime(date("d.m.Y 23:59:59", strtotime($this->data->end)));
        return Certificate::whereBetween('date', [$start, $end])->get();
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $columns = ["A", "B", "C", "D", "E", "F"];
                foreach ($columns as $column) {

                    $event->sheet->getStyle($column)->getAlignment()->setHorizontal('left');

                    $event->sheet->getDelegate()->getStyle($column)->getFont()->setSize(12);

                    $event->sheet->getDelegate()->getStyle('A1:L1')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFCC99');

                    $event->sheet->getDelegate()->getStyle('A1:L1')
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $event->sheet->getColumnDimension($column);
                    $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(40);
                    $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(30);
                    $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(10);
                    $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('J')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('K')->setWidth(20);
                    $event->sheet->getDelegate()->getColumnDimension('L')->setWidth(20);
                }

            }
        ];
    }
}
