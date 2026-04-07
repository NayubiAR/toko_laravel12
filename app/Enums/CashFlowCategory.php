<?php

namespace App\Enums;

enum CashFlowCategory: string
{
    case Sale = 'sale';
    case OtherIncome = 'other_income';
    case Purchase = 'purchase';
    case Operational = 'operational';
    case Salary = 'salary';
    case Tax = 'tax';
    case OtherExpense = 'other_expense';

    public function label(): string
    {
        return match($this) {
            self::Sale => 'Penjualan',
            self::OtherIncome => 'Pendapatan Lain',
            self::Purchase => 'Pembelian Barang',
            self::Operational => 'Operasional',
            self::Salary => 'Gaji',
            self::Tax => 'Pajak',
            self::OtherExpense => 'Pengeluaran Lain',
        };
    }

    public function type(): CashFlowType
    {
        return match($this) {
            self::Sale, self::OtherIncome => CashFlowType::Income,
            default => CashFlowType::Expense,
        };
    }
}
