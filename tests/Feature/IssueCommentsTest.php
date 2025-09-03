<?php

use App\Models\User;
use App\Models\Issue;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('stores a comment and returns JSON + html', function () {
    $user = User::factory()->create();
    $project = Project::factory()->create(['owner_id' => $user->id]);
    $issue = Issue::factory()->create(['project_id' => $project->id]);

    $this->actingAs($user)
        ->postJson(route('issues.comments.store', $issue), ['body' => 'Hello'])
        ->assertOk()
        ->assertJson(fn ($json) =>
            $json->where('ok', true)
                 ->whereType('html', 'string')
                 ->where('comment.user.id', $user->id)
                 ->etc()
        );

    $this->assertDatabaseHas('comments', [
        'issue_id' => $issue->id,
        'user_id'  => $user->id,
        'body'     => 'Hello',
    ]);
});
