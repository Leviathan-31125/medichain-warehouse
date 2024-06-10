<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceAccess extends Model
{
    use HasFactory;

    protected $table = 't_service';
    protected $primaryKey = 'keyAccess';
    protected $guarded = [
        'keyAccess',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;
}
