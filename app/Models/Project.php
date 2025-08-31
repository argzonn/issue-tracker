<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'deadline',
        'user_id',     // owner
        'is_public',   // visibility toggle
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'deadline'   => 'datetime',
        'is_public'  => 'boolean',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
