<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class batteryMastModel extends Model
{
    protected $table = 'battery_master';

    protected $fillable = [
        'serial_no',
        'categoryId',
        'sub_category',
        'MFD',
        'warranty_period',
        'created_by',
        'updated_by',
    ];

    public function category()
    {
        return $this->belongsTo(categoryModel::class, 'categoryId','id');
    }

    public function subCategory()
    {
        return $this->belongsTo(subCategoryModel::class, 'sub_category');
    }

}
