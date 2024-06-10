<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 't_brand';
    protected $primaryKey = 'fc_brandcode';
    public $incrementing = false;
    protected $guarded = [
        'fc_brandcode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function stock() {
        return $this->hasMany(Stock::class, 'fc_brandcode', 'fc_brandcode');
    }
}
