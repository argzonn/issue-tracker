<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class ListUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list {--with-issues : Include issue counts for owned projects}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all users and the projects they own';

    /**
     * Execute the console command.
     */
    public function handle(): int
{
    $withIssues = $this->option('with-issues');

    $users = User::with($withIssues ? ['projects.issues'] : ['projects'])->get();

    $rows = $users->map(function ($u) use ($withIssues) {
        $owned = $u->projects->map(function ($p) use ($withIssues) {
            return $withIssues
                ? "{$p->name} ({$p->issues->count()} issues)"
                : $p->name;
        })->join(', ');

        return [
            'ID' => $u->id,
            'Name' => $u->name,
            'Email' => $u->email,
            'Projects Owned' => $owned,
        ];
    });

    $this->table(['ID', 'Name', 'Email', 'Projects Owned'], $rows);

    return Command::SUCCESS;
}

}
