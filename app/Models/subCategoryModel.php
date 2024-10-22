<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class subCategoryModel extends Model
{
    protected $table = 'sub_category_master';

    protected $fillable = [
        'categoryId',
        'sub_category_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];
}
