<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CtaInteraction extends Model
{
    protected $table = 'cta_interactions';

    protected $fillable = [
        'cta_id',
        'veh_id',
        'cta_type',
        'acted_at',
    ];

    protected $casts = [
        'acted_at' => 'datetime',
    ];

    /**
     * Vehicle associated with this CTA interaction.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'veh_id');
    }
}