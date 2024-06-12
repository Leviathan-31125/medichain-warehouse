<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvStore extends Model
{
    use HasFactory;

    protected $table = 't_invstore';
    protected $primaryKey = 'fc_barcode';
    protected $guarded = [
        'fc_barcode',
        'fc_stockcode',
        'fc_warehousecode',
        'fm_hpp',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $incrementing = false;

    public function warehouse () {
        return $this->hasOne(Warehouse::class, 'fc_warehousecode', 'fc_warehousecde');
    }

    // public function stock () {
    //     return $this->hasOne(Stock::class, 'fc_barcode', 'fc_barcode');
    // }

    public function getStockAttribute () {
        return Stock::where('fc_barcode', $this->int_barcode)->first();
    }

    public function getIntBarcodeAttribute () {
        return substr($this->fc_barcode, 0, 30);
    }

    protected $appends= ['stock'];
}
