<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TempDoDTL extends Model
{
    use HasFactory;

    protected $table = 't_temp_dodtl';
    protected $primaryKey = 'fn_rownum';
    public $guarded = [
        'fn_rownum',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public $incrementing = false;

    public function invstore () {
        return $this->hasOne(InvStore::class, 'fc_barcode', 'fc_barcode');
    }

    public function tempdomst () {
        return $this->hasOne(TempDoDMST::class, 'fc_dono', 'fc_dono');
    }
}
