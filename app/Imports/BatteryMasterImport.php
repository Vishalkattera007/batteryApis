<?php

namespace App\Imports;

use App\Models\batteryMastModel;
use Exception;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class BatteryMasterImport implements ToModel, WithHeadingRow
{

    protected $category_id;
    protected $subcategory_id;

    public function __construct($category_id, $subcategory_id)
    {
        $this->category_id = $category_id;
        $this->subcategory_id = $subcategory_id;
    }

    public function model(array $row)
    {
        Log::info('Importing row:', $row);

        $existing_serial_no = batteryMastModel::where('serial_no', $row['serial_no'])->first();

        if ($existing_serial_no) {
            Log::info('Serial no is already existing', ['serial_no' => $row['serial_no']]);
            return null;
        }

        $mfdcheck = null; // Initialize $mfdcheck to null

        if (isset($row['mfd'])) {
            Log::info('Raw MFD Value:', ['MFD' => $row['mfd']]);

            try {
                if (is_numeric($row['mfd'])) {
                    $mfdcheck = Date::excelToDateTimeObject($row['mfd'])->format('Y-m-d');
                } elseif (strtotime($row['mfd'])) {
                    $mfdcheck = date('Y-m-d', strtotime($row['mfd']));
                }
            } catch (Exception $e) {
                Log::error('Error parsing MFD', ['mfd' => $row['mfd'], 'error' => $e->getMessage()]);
            }
        }

        if ($mfdcheck === null) {
            Log::warning('Invalid MFD Date', ['mfd' => $row['mfd']]);
        }

        return new batteryMastModel([

            'categoryId' => $this->category_id,
            'sub_category' => $this->subcategory_id,
            'serial_no' => $row['serial_no'] ?? null,
            'MFD' => $mfdcheck,
            'warranty_period' => $row['warranty_period'] ?? null,
            'prowarranty_period' => $row['prowarranty_period'] ?? null,

        ]);
    }
}
