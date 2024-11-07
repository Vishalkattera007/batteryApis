<?php

namespace App\Imports;

use App\Models\subCategoryModel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubcategoryImport implements ToModel, WithHeadingRow
{
    /**
     * @param Collection $collection
     */
    public function model(array $row)
    {
        Log::info('Importing Row:', $row);

        return new subCategoryModel([
            'categoryId' => 1,
            'sub_category_name' => $row['sub_category_name'] ?? null,
        ]);
    }
}
