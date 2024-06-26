<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoMST extends Model
{
    use HasFactory;

    protected $table = 't_domst';
    protected $primaryKey = 'fc_dono';
    public $guarded = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $incrementing = false;

    public function dodtl () {
        return $this->hasMany(DoDTL::class, 'fc_dono', 'fc_dono');
    }

    public function warehouse () {
        return $this->hasOne(Warehouse::class, 'fc_warehousecode', 'fc_warehousecode');
    }

    public function somst () {
        return $this->hasOne(SOMST::class, 'fc_sono', 'fc_sono');
    }
}
