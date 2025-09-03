<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // Make writes impossible to fail because of mass-assignment rules
    protected $guarded = []; // ['issue_id','author_name','body'] would also work

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
