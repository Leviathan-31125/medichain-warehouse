<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $table = 't_customer';
    protected $primaryKey = 'fc_membercode';
    public $incrementing = false;
    protected $guarded = [
        'fc_membercode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function somst() {
        return $this->hasMany(SOMST::class, 'fc_membercode', 'fc_membercode');
    }
}
