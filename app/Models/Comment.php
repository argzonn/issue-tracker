<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    // We set issue_id via relation (issue()->create()), so no need to fill it directly.
    protected $fillable = ['author_name', 'body'];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }
}
