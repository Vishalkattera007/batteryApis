<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class subCategoryModel extends Model
{
    protected $table = 'sub_category_master';

    protected $fillable = [
        'category_id',        // Use 'category_id' to match the foreign key in your database
        'sub_category_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    // Define the relationship with CategoryModel
    public function category()
    {
        return $this->belongsTo(CategoryModel::class, 'category_id', 'id'); // Ensure the foreign key is correct
    }

    // Define the relationship with BatteryMastModel
    public function batteries()
    {
        return $this->hasMany(BatteryMastModel::class, 'sub_category', 'id');
    }
}
