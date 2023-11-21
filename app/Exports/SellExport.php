<?php

namespace App\Exports;

use App\Models\Sell;
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
       return $row;
    }

    public function collection()
    {
        $start = strtotime(date("d.m.Y 00:00:00", strtotime($this->data->start)));
        $end = strtotime(date("d.m.Y 23:59:59", strtotime($this->data->end)));
        return Sell::whereBetween('closed', [$start, $end])->get();
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
