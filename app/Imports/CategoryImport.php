<?php

namespace App\Imports;

use App\Models\categoryModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        Log::info('Importing Row: ', $row);

        return new categoryModel([
            'name'=> $row['name']??null,
        ]);
    }
}
