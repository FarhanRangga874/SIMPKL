<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class PklPeriod extends Model
{
    protected $fillable = [
        'semester',
        'tahun_akademik',
        'status',
    ];

    public function batches(): HasMany
    {
        return $this->hasMany(PklBatch::class, 'pkl_period_id');
    }

    public function cpls(): BelongsToMany
    {
        return $this->belongsToMany(
            Cpl::class,
            'pkl_period_cpl',
            'pkl_period_id',
            'kode_cpl',
            'id',
            'kode_cpl'
        )->withTimestamps();
    }
}