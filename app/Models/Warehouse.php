<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $table = 't_warehouse';
    protected $primaryKey = 'fc_warehousecode';
    public $guarded = [
        'fc_warehousecode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
