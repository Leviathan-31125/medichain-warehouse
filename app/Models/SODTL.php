<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SODTL extends Model
{
    use HasFactory;

    protected $table = 't_sodtl';
    protected $primaryKey = 'fn_rownum';
    protected $guarded = [
        'fn_rownum',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    public $incrementing = false;

    public function somst () {
        return $this->hasOne(SOMST::class, 'fc_sono', 'fc_sono');
    }
}
