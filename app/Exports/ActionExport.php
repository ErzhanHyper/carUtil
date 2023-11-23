<?php

namespace App\Exports;

use App\Models\Log;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ActionExport implements FromCollection, WithMapping, WithHeadings, WithHeadingRow, WithEvents
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
        return Log::whereBetween('when', [$start, $end])->get();
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
            '№ действия',
            'Тип действия',
            'Тип объекта',
            '№ объекта',
            'Дата и время',
            'ИИН автора',
            'ФИО автора',
            'Роль',
        ];
    }

    public function map($row): array
    {
        $user = User::find($row->user_id);

        return [
            $row->id,
            $row->event,
            $row->object_type,
            $row->object_id,
            date('d.m.Y', $row->when),
            $user ? $user->login. ' ' : '-' ,
            $user ? $user->title : '-',
            $user ? $user->role : '-'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $columns = ["A", "B", "C", "D", "E", "F", "G", "H"];
                foreach ($columns as $column) {

                    $event->sheet->getStyle($column)->getAlignment()->setHorizontal('left');

                    $event->sheet->getDelegate()->getStyle($column)->getFont()->setSize(12);

                    $event->sheet->getDelegate()->getStyle('A1:H1')
                        ->getFill()
                        ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB('FFCC99');

                    $event->sheet->getDelegate()->getStyle('A1:H1')
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
                }
            }
        ];
    }
}
