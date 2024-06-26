<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiriBP extends Model
{
    use HasFactory;

    protected $table = 't_inquiristock_bp';
    protected $primaryKey = 'fc_inquirycode';
    protected $guarded = [
        'fc_inquirycode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $incrementing = false;

    public function invstore(){
        return $this->hasOne(InvStore::class, 'fc_barcode', 'fc_barcode');
    }

    public function warehouse(){
        return $this->hasOne(Warehouse::class, 'fc_warehousecode', 'fc_warehousecode');
    }
}
