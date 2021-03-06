<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class follower extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'follow_id',
        'is_friend'
    ];

    protected $hidden = [
        'updated_at'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);

    }

    public function follows()
    {
        return $this->belongsTo(User::class, 'follow_id');
    }
}
