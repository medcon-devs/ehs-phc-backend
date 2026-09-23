<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sponser extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'logo', 'event_id'];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
