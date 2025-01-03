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

        $existingSubcategory = subCategoryModel::where('sub_category_name', $row['sub_category_name'])
            ->where('categoryId', $this->category_id)
            ->exists();

        if ($existingSubcategory) {
            throw new \Exception("Duplicate entry found, Kindle Check.");

        }

        return new subCategoryModel([
            'categoryId' => $this->category_id ?? null,
            'sub_category_name' => $row['sub_category_name'] ?? null,
            'warranty_period' => $row['warranty_period'] ?? null,
            'prowarranty_period' => $row['prowarranty_period'] ?? null,

        ]);
    }
}
