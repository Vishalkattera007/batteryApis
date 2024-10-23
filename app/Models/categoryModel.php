<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categoryModel extends Model
{
    // Define the corresponding table name
    protected $table = 'category_master';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    // Relationship with BatteryMastModel
    public function batteries()
    {
        return $this->hasMany(BatteryMastModel::class, 'category', 'id'); // Correct if 'category' is the foreign key
    }

    // Relationship with SubCategoryModel
    public function subCategories()
    {
        return $this->hasMany(SubCategoryModel::class, 'category_id', 'id'); // Adjust 'category_id' if your foreign key is named differently
    }
}
