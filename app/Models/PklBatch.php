<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PklBatch extends Model
{
    protected $fillable = [
        'pkl_period_id',
        'nama_batch',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(PklPeriod::class, 'pkl_period_id');
    }
}