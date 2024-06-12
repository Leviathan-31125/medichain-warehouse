<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SOMST extends Model
{
    use HasFactory;

    protected $table = 't_somst';
    protected $primaryKey = 'fc_sono';
    public $guarded = [
        'fc_sono',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;

    public function sodtl () {
        return $this->hasMany(SODTL::class, 'fc_sono', 'fc_sono');
    }

    public function customer () {
        return $this->hasOne(Customer::class, 'fc_membercode', 'fc_membercode');
    }
}
