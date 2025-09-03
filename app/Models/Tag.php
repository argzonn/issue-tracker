<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color'];

    public function setColorAttribute($value): void
    {
        $val = trim((string) $value);

        if ($val !== '' && $val[0] !== '#') {
            $val = '#' . $val;
        }
        // Optional hardening:
        // if (preg_match('/^#([0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/', $val) !== 1) {
        //     $val = null;
        // }

        $this->attributes['color'] = $val;
    }

    public function issues(): BelongsToMany
    {
        return $this->belongsToMany(Issue::class);
    }
}
