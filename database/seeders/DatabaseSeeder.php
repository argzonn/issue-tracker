<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Project::factory(3)->create();

        Issue::factory(18)->create();

        $tags = Tag::factory(8)->create();

        // attach random tags
        Issue::all()->each(function($issue) use ($tags) {
            $issue->tags()->sync($tags->random(rand(0,3))->pluck('id')->all());
        });

        // comments
        Comment::factory(40)->create();
    }
}
