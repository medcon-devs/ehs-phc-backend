<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $fillable = [
        "url", "mediable_id", "group_key", "mediable_type", "type"
    ];
}
