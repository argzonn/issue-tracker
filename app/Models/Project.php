<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'deadline',
        'owner_id',   // single, consistent owner FK
        'is_public',  // keep only if the column exists
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline'   => 'date',
        'is_public'  => 'boolean',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
