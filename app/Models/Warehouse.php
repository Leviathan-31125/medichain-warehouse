<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_warehouse';
    protected $primaryKey = 'fc_warehousecode';
    public $guarded = [
        'fc_warehousecode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $incrementing = false;

    public function invstore () {
        return $this->hasMany(InvStore::class, 'fc_warehousecode', 'fc_warehousecode');
    }
}
