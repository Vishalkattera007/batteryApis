<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignBatteryModel extends Model
{
    //
    protected $table = 'assigned_batteries';

    protected $fillable = [
        'dealer_id',
        'catergory_id',
        'sub_category_id',
        'nof_batteries',
        'created_by',
        'updated_by',
    ];

    public function dealer()
    {
        return $this->belongsTo(DealerModel::class, 'dealer_id');
    }

    public function category()
    {
        return $this->belongsTo(categoryModel::class, 'catergory_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(subCategoryModel::class, 'sub_category_id');
    }

}
