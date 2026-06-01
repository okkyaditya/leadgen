<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return [
            'ID', 'Nama Prospek', 'Telepon', 'NIK', 'Produk', 
            'Potensial NTF', 'Unit', 'No Unit', 'Cabang', 
            'Domisili', 'Input Oleh', 'Sumber Mitra', 'Tanggal Dibuat'
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->nama,
            $lead->telepon,
            $lead->nik,
            $lead->produk,
            $lead->ntf,
            $lead->unit,
            $lead->no_unit,
            $lead->cabang,
            $lead->domisili,
            $lead->inputBy?->nama ?? '-',
            $lead->sourceMitra?->nama ?? '-',
            $lead->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
