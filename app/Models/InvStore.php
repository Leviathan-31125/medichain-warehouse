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
}
