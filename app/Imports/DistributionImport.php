<?php

namespace App\Imports;

// use Illuminate\Support\Collection;

use App\Models\batteryMastModel;
use App\Models\DistributionBatteryModel;
use Illuminate\Support\Facades\Log;
// use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class DistributionImport implements ToModel, WithHeadingRow
{
    // /**
    // * @param Collection $collection
    // */

    protected $dealer_id;
    protected $type_dist;
    protected $created_by;


    public function __construct($dealer_id, $created_by, $type_dist)
    {
        // $this->category_id = $category_id;
        $this->dealer_id = $dealer_id;
        $this->type_dist = $type_dist;
        $this->created_by = $created_by;

        // $this->subcategory_id = $subcategory_id;
    }

    public function model(array $row)
    {
        //

        Log::info('Importing row:', $row);

        $existing_serial_no = batteryMastModel::where('serial_no', $row['serial_no'])->first();

        if ($existing_serial_no) {

            return new DistributionBatteryModel([

                'dealer_id' => $this->dealer_id,
                'specification_no' => $row['serial_no'] ?? null,
                'type_of_distribution' => $this->type_dist,
                'created_by' => $this->created_by
    
            ]);
           
        }else{
            Log::error('Serial is not already existing', ['serial_no' => $row['serial_no']]);
            return null;
        }

        

    }
}
