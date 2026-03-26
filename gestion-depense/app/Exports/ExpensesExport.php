<?php

namespace App\Exports;

use App\Models\Expense;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function headings(): array
    {
        return [
            'Date',
            'Catégorie',
            'Description',
            'Montant (Ar)'
        ];
    }

    public function map($expense): array
    {
        return [
            date('d/m/Y', strtotime($expense->expense_date)),
            $expense->category ? $expense->category->name : 'N/A',
            $expense->description,
            $expense->amount
        ];
    }
}
