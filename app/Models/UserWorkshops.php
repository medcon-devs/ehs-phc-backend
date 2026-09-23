<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserWorkshops extends Model
{
    protected $table = "user_workshop";

    protected $fillable = [
        'user_id', 'option_1', 'option_2'
    ];

    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
