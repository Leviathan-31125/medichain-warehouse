<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Stock extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_stock';
    protected $primaryKey = 'fc_barcode';
    protected $guarded = [
        'fc_barcode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $incrementing = false;

    public function brand() {
        return $this->belongsTo(Brand::class, 'fc_brandcode', 'fc_brandcode');
    }

    // public function tempsodtl () {
    //     return $this->hasMany(TempSODTL::class, 'fc_barcode', 'fc_barcode');
    // }

    // public function sodtl () {
    //     return $this->hasMany(SODTL::class, 'fc_barcode', 'fc_barcode');
    // }
}