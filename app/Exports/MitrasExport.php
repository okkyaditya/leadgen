<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MitrasExport implements FromCollection, WithHeadings, WithMapping
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
            'ID', 'NIK', 'Nama Lengkap', 'Telepon', 'Email', 
            'Profesi', 'Tanggal Lahir', 'Domisili', 'Upline', 
            'Status', 'Alasan Nonaktif', 'Tanggal Dibuat'
        ];
    }

    public function map($mitra): array
    {
        return [
            $mitra->id,
            $mitra->nik,
            $mitra->nama,
            $mitra->telepon,
            $mitra->email,
            $mitra->profesi,
            $mitra->tanggal_lahir ? $mitra->tanggal_lahir->format('Y-m-d') : '-',
            $mitra->domisili,
            $mitra->upline?->nama ?? '-',
            $mitra->is_active ? 'Aktif' : 'Nonaktif',
            $mitra->is_active_reason ?? '-',
            $mitra->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
