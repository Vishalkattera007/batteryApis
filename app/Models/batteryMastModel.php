<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class batteryMastModel extends Model
{
    // Define the corresponding table name
    protected $table = 'battery_master';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'serial_no',
        'categoryId',
        'sub_category',
        'MFD',                // Manufacturer's Date
        'warranty_period',
        'created_by',
        'updated_by',
    ];

    // Relationship with CategoryModel
    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'categoryId'); // Foreign key in battery_master
    }

    // Relationship with SubCategoryModel
    public function subCategory()
    {
        return $this->belongsTo(SubCategoryModel::class, 'sub_category'); // Foreign key in battery_master
    }
}
