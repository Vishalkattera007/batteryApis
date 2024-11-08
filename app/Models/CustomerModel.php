<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerModel extends Model
{
    //

    protected $table='customer_master';
    
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'phoneNumber',
        'pincode',
        'created_by',
        'updated_by'
    ];


    public function batteries(){
        return $this->hasMany(batteryRegModel::class, 'customer_id');
    }

   
}
