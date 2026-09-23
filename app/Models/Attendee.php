<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendee extends Model
{
    use HasFactory;

    protected $fillable = ['user_id']; // Fields that are mass assignable

    // Ensure created_at is shown in Dubai time
    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->timezone('Asia/Dubai');
    }

    // Relationship: Attendee belongs to a User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
