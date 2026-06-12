<?php

namespace App\Enums;

enum ExpenseOpts: string
{
    case Electricity = 'electricity';
    case DriverBata = 'driver_bata';
    case Salary = 'salary';
    case Maintenance = 'maintenance';
    case Diesel = 'diesel';
    case Petrol = 'petrol';

    public static function displayValz(ExpenseOpts $vl): string
    {
        return ucfirst(str_replace('_', ' ', $vl->value));
    }
}
