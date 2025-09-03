<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_root_redirects_to_projects_index(): void
    {
        // Laravel >=10 has assertRedirectToRoute; if not, use assertRedirect(route(...)) below.
        $this->get('/')->assertRedirectToRoute('projects.index');
        // $this->get('/')->assertRedirect(route('projects.index')); // fallback
    }

    public function test_projects_index_is_publicly_accessible(): void
    {
        $this->get(route('projects.index'))->assertOk();
    }
}
