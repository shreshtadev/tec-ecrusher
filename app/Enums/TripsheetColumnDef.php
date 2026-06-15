<?php

namespace App\Enums;

class TripsheetColumnDef
{
    public static function columns(): array
    {
        return [
            'invoice.invoice_number' => 'Invoice',
            'challan_number' => 'Challan No',
            'party.full_name' => 'Party',
            'driver.full_name' => 'Driver',
            'vehicle.vehicle_number' => 'Vehicle',
            'item.material_name' => 'Item',
            'quantity_cft' => 'Quantity',
            'challan_date' => 'Challan Date',
        ];
    }

    public static function columnsByParty(): array
    {
        return [
            'date' => 'Date',
            'time' => 'Time',
            'challan_number' => 'Challan No',
            'party' => 'Party',
            'vehicle' => 'Vehicle',
            'driver' => 'Driver',
            'invoice' => 'Invoice No',
            'item' => 'Item',
            'quantity_cft' => 'Quantity (CFT)',
            'rate_at_sale' => 'Rate',
            'amount' => 'Amount',
        ];
    }
}
