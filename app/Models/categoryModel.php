<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categoryModel extends Model
{
    // Define the corresponding table name
    protected $table = 'category_master';

    // Fillable attributes for mass assignment
    protected $fillable = [
        'id',
        'name',
        'shortcode',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function batteries(){
        return $this->hasMany(batteryMastModel::class, 'category', 'id');
    }
    

}
