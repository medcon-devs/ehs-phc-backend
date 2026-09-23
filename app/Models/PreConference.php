<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreConference extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        "agenda_date",
        "order",
        "event_id",
    ];
    protected $table = 'pre_conference';
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
    public function details(): HasMany
    {
        return $this->hasMany(PreConferenceDetails::class, 'agenda_id');
    }
}
