<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User; // <-- add this

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Project::factory(3)->create();

        Issue::factory(18)->create();

        $tags = Tag::factory(8)->create();

        Issue::all()->each(function ($issue) use ($tags) {
            $issue->tags()->sync($tags->random(rand(0,3))->pluck('id')->all());
        });

        Comment::factory(40)->create();

        // --- NEW: seed users and assign to issues ---
        $users = User::factory()->count(5)->create();

        Issue::all()->each(function ($issue) use ($users) {
            $ids = $users->random(rand(0, min(3, $users->count())))->pluck('id')->all();
            if ($ids) {
                $issue->assignees()->syncWithoutDetaching($ids);
            }
        });
    }
}
