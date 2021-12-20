<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
class PayrollExport implements WithDrawings,FromArray,WithCustomStartCell,ShouldAutoSize,WithHeadings
{
    use Exportable;
    
    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('RaudraTech');
        $drawing->setDescription('RaudraTech');
        $drawing->setPath('/var/www/html/public/admin_asset/assets/plugins/images/logo_pdf.png');
        $drawing->setHeight(90);
        $drawing->setCoordinates('A1');
        
       
        return [$drawing];
    }
    
    
    

    protected $invoices;

    public function __construct(array $invoices)
    {
        $this->invoices = $invoices;
    }

    public function array(): array
    {
        return $this->invoices;
    }
    public function startCell(): string
    {
        return 'A7';
    }
    
    public function headings(): array
    {
        return [
           ['BLOCK/C-118, SWAGAT RAIN FOREST 2, OPP. SWAMINARAYAN DHAM'],
           ['KOBA - GANDHINAGAR HIGHWAY, KUDASAN, GANDHINAGAR Gandhinagar Gujarat 382421'],
            ["    ",""],
        ];
    }
    
    
}