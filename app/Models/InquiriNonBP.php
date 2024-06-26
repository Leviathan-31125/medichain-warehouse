<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InquiriNonBP extends Model
{
    use HasFactory;

    protected $table = 't_inquiristock_nonbp';
    protected $primaryKey = 'fc_inquirycode';
    protected $guarded = [
        'fc_inquirycode',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
