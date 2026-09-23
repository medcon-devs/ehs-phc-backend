<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faculty extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subtitle',
        'brief',
        'biography',
        'profile',
        'vip',
        'order',
    ];

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class);
    }
}
