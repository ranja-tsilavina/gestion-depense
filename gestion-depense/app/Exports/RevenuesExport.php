<?php

namespace App\Exports;

use App\Models\Revenue;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class RevenuesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $revenues;

    public function __construct($revenues)
    {
        $this->revenues = $revenues;
    }

    public function collection()
    {
        return $this->revenues;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Source',
            'Description',
            'Montant (Ar)'
        ];
    }

    public function map($revenue): array
    {
        return [
            date('d/m/Y', strtotime($revenue->revenue_date)),
            $revenue->source,
            $revenue->description,
            $revenue->amount
        ];
    }
}
