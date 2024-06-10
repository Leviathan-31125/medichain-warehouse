<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempDoDMST extends Model
{
    use HasFactory;

    protected $table = 't_temp_domst';
    protected $primaryKey = 'fc_dono';
    public $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $incrementing = false;

    public function tempdodtl () {
        return $this->hasMany(TempDODTL::class, 'fc_dono', 'fc_dono');
    }

    public function warehouse () {
        return $this->hasOne(Warehouse::class, 'fc_warehousecode', 'fc_warehousecode');
    }
}