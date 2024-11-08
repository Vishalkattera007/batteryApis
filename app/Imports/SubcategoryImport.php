<?php

namespace App\Imports;

use App\Models\subCategoryModel;
// use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SubcategoryImport implements ToModel, WithHeadingRow
{

    protected $category_id;

    public function __construct($category_id)
    {
        $this->category_id = $category_id;
    }

    public function model(array $row)
    {
        Log::info('Importing Row:', $row);

        $existingSubCategory = subCategoryModel::where('categoryId', $this->category_id)
            ->where('sub_category_name', $row['sub_category_name'])
            ->first();

        if ($existingSubCategory) {
            Log::warning('Duplicate Entry Skipped:', $row);
            return null; 
        }

        return new subCategoryModel([
            'categoryId' => $this->category_id ?? null,
            'sub_category_name' => $row['sub_category_name'] ?? null,
        ]);
    }
}
