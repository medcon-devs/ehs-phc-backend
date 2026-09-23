<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'title',
        'image',
        'subtitle',
        'message_header',
        'message_content',
        'order',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
