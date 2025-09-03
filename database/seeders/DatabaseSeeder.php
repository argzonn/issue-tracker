<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Project;
use App\Models\Issue;
use App\Models\Tag;
use App\Models\Comment;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // --- Users ----------------------------------------------------------
        $owner = User::firstOrCreate(
            ['email' => 'owner@example.com'],
            [
                'name'              => 'Owner Demo',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'remember_token'    => Str::random(40),
            ]
        );

        $member = User::firstOrCreate(
            ['email' => 'member@example.com'],
            [
                'name'              => 'Member Demo',
                'email_verified_at' => now(),
                'password'          => bcrypt('password'),
                'remember_token'    => Str::random(40),
            ]
        );

        // --- Tags -----------------------------------------------------------
        if (Tag::count() === 0) {
            $palette = [
                ['name' => 'bug',     'color' => '#dc3545'],
                ['name' => 'feature', 'color' => '#0d6efd'],
                ['name' => 'urgent',  'color' => '#fd7e14'],
                ['name' => 'ui',      'color' => '#6f42c1'],
                ['name' => 'backend', 'color' => '#198754'],
            ];
            foreach ($palette as $t) {
                Tag::firstOrCreate(['name' => $t['name']], ['color' => $t['color']]);
            }
        }
        $allTags = Tag::orderBy('name')->get();

        // --- Projects (Owner) ----------------------------------------------
        // 3 public + 2 private owned by Owner
        $ownerPublic  = Project::factory()->count(3)->create([
            'user_id'   => $owner->id,
            'is_public' => true,
        ]);

        $ownerPrivate = Project::factory()->count(2)->create([
            'user_id'   => $owner->id,
            'is_public' => false,
        ]);

        // --- Projects (Unowned) --------------------------------------------
        // 2 public (unowned)
        $unownedPublic = Project::factory()->count(2)->create([
            'user_id'   => null,
            'is_public' => true,
        ]);

        // 1 private (unowned)
        $unownedPrivate = Project::factory()->count(1)->create([
            'user_id'   => null,
            'is_public' => false,
        ]);

        // Combine all projects for issue seeding
        $projects = $ownerPublic
            ->concat($ownerPrivate)
            ->concat($unownedPublic)
            ->concat($unownedPrivate);

        // --- Issues + Comments + Assignees ---------------------------------
        foreach ($projects as $project) {
            // 4–6 issues per project
            $issueCount = rand(4, 6);
            $issues = Issue::factory()
                ->count($issueCount)
                ->create([
                    'project_id' => $project->id,
                ]);

            foreach ($issues as $issue) {
                // Attach 0–3 random tags
                if ($allTags->isNotEmpty()) {
                    $pick = $allTags->shuffle()->take(rand(0, 3))->pluck('id')->all();
                    if (!empty($pick)) {
                        $issue->tags()->syncWithoutDetaching($pick);
                    }
                }

                // 1–2 comments (seeded as plain names)
                $commentsToMake = rand(1, 2);
                for ($i = 0; $i < $commentsToMake; $i++) {
                    $author = fake()->name();
                    Comment::create([
                        'issue_id'    => $issue->id,
                        'author_name' => $author,
                        'body'        => fake()->sentence(rand(8, 16)),
                    ]);
                }

                // Assignees (bonus): randomly assign owner/member
                $assignees = [];
                if (rand(0, 1)) { $assignees[] = $owner->id; }
                if (rand(0, 1)) { $assignees[] = $member->id; }
                if (!empty($assignees)) {
                    $issue->assignees()->syncWithoutDetaching($assignees);
                }
            }
        }

        // Output summary in console
        $summary = [
            'users' => User::count(),
            'projects_public'  => Project::where('is_public', true)->count(),
            'projects_private' => Project::where('is_public', false)->count(),
            'issues' => Issue::count(),
            'tags'   => Tag::count(),
        ];
        dump($summary);
    }
}
