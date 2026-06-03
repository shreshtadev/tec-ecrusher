<?php

namespace App\Domains\Common\Enums;

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
            'created_at' => 'Created At',
        ];
    }
}
