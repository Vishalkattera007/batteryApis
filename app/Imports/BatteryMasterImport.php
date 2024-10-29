<?php

namespace App\Imports;


use App\Models\BatteryMastModel;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BatteryMasterImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        Log::info('Importing row:', $row);

        
        return new BatteryMastModel([
            
            //
            'serial_no'        => $row['serial_no'] ?? null,
            'categoryId'       => $row['categoryid']??null,
            'sub_category'     => $row['sub_category'] ?? null,
            'MFD'              => Date::excelToDateTimeObject($row['mfd'])->format('Y-m-d'),
            'warranty_period'  => $row['warranty_period'] ?? null,
            // 'created_by'       => auth()->id(),             // Or another value if not using auth
            // 'updated_by'       => auth()->id(),    
        ]);
    }
}
