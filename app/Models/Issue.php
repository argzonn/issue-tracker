<?php declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    use HasFactory;

    /** Single source of truth for allowed values (lowercase). */
    public const STATUSES   = ['open', 'in_progress', 'closed'];
    public const PRIORITIES = ['low', 'medium', 'high', 'urgent'];

    protected $fillable = [
        'project_id',
        'title',
        'description',
        'status',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'date',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function tags()
    {
        return $this->belongsToMany(\App\Models\Tag::class)->withTimestamps();

    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->latest();
    }

    public function assignees()
    {
        return $this->belongsToMany(\App\Models\User::class, 'issue_user')->withTimestamps();
    }

    // Scopes
    public function scopeKeyword(Builder $q, ?string $kw): Builder
    {
        if (!($kw = trim((string) $kw))) {
            return $q;
        }
        $terms = preg_split('/\s+/', $kw, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        return $q->where(function (Builder $outer) use ($terms) {
            foreach ($terms as $t) {
                $like = "%{$t}%";
                $outer->where(function (Builder $w) use ($like) {
                    $w->where('title', 'like', $like)
                      ->orWhere('description', 'like', $like);
                });
            }
        });
    }

    public function scopeStatus(Builder $q, ?string $status): Builder
    {
        return $status ? $q->where('status', $status) : $q;
    }

    public function scopePriority(Builder $q, ?string $priority): Builder
    {
        return $priority ? $q->where('priority', $priority) : $q;
    }

    public function scopeTag(Builder $q, ?int $tagId): Builder
    {
        return $tagId ? $q->whereHas('tags', fn ($t) => $t->where('tags.id', $tagId)) : $q;
    }

    public function scopeForList(Builder $q): Builder
    {
        return $q->with(['project'])->withCount('comments');
    }
}
