<?php

namespace App\Exports;

use App\Models\Car;
use App\Models\Exchange;
use App\Models\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use phpseclib3\File\ASN1\Maps\Certificate;

class ExchangeExport implements FromCollection, WithMapping, WithHeadings, WithHeadingRow, WithEvents
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
        return Exchange::whereBetween('approved', [$start, $end])->get();
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function headings(): array
    {
        return [
            '№ переоформления',
            'Номер сертификата',
            'Дата сертификата',
            'ИИН / БИН (старый)',
            'Старый владелец',
            'ИИН / БИН (новый)',
            'Новый владелец',
            'Дата передачи',
            'Категория сертификата'
        ];
    }

    public function map($row): array
    {
        $cert = Certificate::find($row->certificate_id);

        $log = Log::where('event', 'exchange')->where('object_id', $row->id)->orderByDesc('id')->first();
        $old = json_decode($log->object_before, true);

        $car = Car::find($cert->car_id);

        $old_idnum = '-';
        $old_title = '-';
        if($row->title === $cert->title_1) {
            $old_idnum = $cert->idnum_1;
            $old_title = $cert->title_1;
        }else{
            if($old) {
                $old_idnum = $old->idnum;
                $old_title = $old->title;
            }
        }
        return [
            $row->id,
            $row->certificate_id,
            $cert ? date('d.m.Y', $cert->date) : '-',
            $old_idnum,
            $old_title,
            $row->idnum,
            $row->title,
            date('d.m.Y', $row->created),
            $car && $car->category ? $car->category->title_ru : '-'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $columns = ["A", "B", "C", "D", "E", "F", "G", "H", "I"];
                foreach ($columns as $column) {

                    $event->sheet->getStyle($column)->getAlignment()->setHorizontal('left');

                    $event->sheet->getDelegate()->getStyle($column)->getFont()->setSize(12);

                    $event->sheet->getDelegate()->getStyle('A1:I1')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFCC99');

                    $event->sheet->getDelegate()->getStyle('A1:I1')
                        ->getAlignment()
                        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                    $event->sheet->getColumnDimension($column);
                    $event->sheet->getDelegate()->getColumnDimension('A')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('B')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('C')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('D')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('E')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('F')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('G')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('H')->setWidth(25);
                    $event->sheet->getDelegate()->getColumnDimension('I')->setWidth(25);
                }
            }
        ];
    }
}
