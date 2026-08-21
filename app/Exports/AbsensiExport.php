<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AbsensiExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function __construct(private array $rows)
    {
    }

    public function array(): array
    {
        return collect($this->rows)->map(fn ($row, $i) => array_replace([$i + 1], $row))->all();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'No. Induk',
            'Divisi',
            'Slot',
            'Tanggal',
            'Jam Scan',
            'Status',
            'Approval Izin',
            'Keterangan',
        ];
    }
}
