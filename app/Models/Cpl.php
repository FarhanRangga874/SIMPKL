<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Cpl extends Model
{
    protected $table = 'cpl';

    protected $primaryKey = 'kode_cpl';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'kode_cpl',
        'deskripsi_cpl',
        'domain_kompetensi',
    ];

    public function pklPeriods(): BelongsToMany
    {
        return $this->belongsToMany(
            PklPeriod::class,
            'pkl_period_cpl',
            'kode_cpl',
            'pkl_period_id',
            'kode_cpl',
            'id'
        );
    }
}