<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OrdersReportExport implements FromCollection, WithHeadings
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return [
            'Order ID',
            'Buy Price',
            'MRP',
            'Sell Price',
            'Discount',
            'Special Discount',
            'Delivery Charge',
            'Payable Price',
            'Customer Phone',
            'Customer Name',
            'District',
            'Address Details',
            'Created At',
        ];
    }

    public function collection()
    {
        return $this->data;
    }
}
