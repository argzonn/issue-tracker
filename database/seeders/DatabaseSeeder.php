<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\User;
use App\Models\Project;
use App\Models\Issue;
use App\Models\Tag;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1) Users first (so later seeders can reference them cleanly)
        $users = User::factory()->count(5)->create();

        // 2) Core data
        Project::factory(3)->create();
        Issue::factory(18)->create();
        $tags = Tag::factory(8)->create();

        // 3) Attach tags to issues (your existing logic)
        Issue::all()->each(function (Issue $issue) use ($tags) {
            $issue->tags()->sync($tags->random(rand(0, 3))->pluck('id')->all());
        });

        // 4) Comments
        Comment::factory(40)->create();

        // 5) Random assignees per issue (0–3)
        Issue::all()->each(function (Issue $issue) use ($users) {
            $ids = $users->random(rand(0, min(3, $users->count())))->pluck('id')->all();
            if ($ids) {
                $issue->assignees()->syncWithoutDetaching($ids);
            }
        });
    }
}
