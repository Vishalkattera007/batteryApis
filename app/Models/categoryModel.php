<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class categoryModel extends Model
{
    protected $table = 'category_master';

    protected $fillable = [
        'id',
        'name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    public function batteries(){
        return $this->hasMany(batteryMastModel::class, 'category', 'id');
    }

}
