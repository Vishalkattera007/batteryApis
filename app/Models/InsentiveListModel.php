<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InsentiveListModel extends Model
{

    protected $table = 'insentive_list';

    protected $fillable = [
        'typeOfInsetive',
        'dealerId',
        'message',
        'status'
    ];

    public function dealer()
    {
        return $this->belongsTo(DealerModel::class, 'dealer_id');
    }
}

