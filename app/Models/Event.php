<?php

namespace App\Models;

use App\Traits\Mediable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Media;

class Event extends Model
{
    use HasFactory, SoftDeletes, Mediable;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'hotel',
        'address',
        'map',
        'event_status',
    ];

    public function getDateAttribute(): string
    {
        $startDate = Carbon::parse($this->start_date)->format('j<\s\u\p>S</\s\u\p>');
        $endDate = Carbon::parse($this->end_date)->format('j<\s\u\p>S</\s\u\p> F Y');

        return "{$startDate} & {$endDate}";
    }

    public function getDays(): string
    {
        $startDate = Carbon::parse($this->start_date)->format('l');
        $endDate = Carbon::parse($this->end_date)->format('l');
        return "{$startDate} & {$endDate}";
    }

    public function agendas(): HasMany
    {
        return $this->hasMany(Agenda::class, 'event_id');
    }

    public function faculties(): BelongsToMany
    {
        return $this->belongsToMany(Faculty::class);
    }



    public function messages()
    {
        return $this->hasMany(EventMessage::class, 'event_id');
    }
    public function sponsors()
    {
        return $this->hasMany(Sponser::class, 'event_id');
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable');
    }


}
