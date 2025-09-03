<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\IssueCommentController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\IssueAssigneeController;
use App\Http\Controllers\Auth\LoginController;

/*
|--------------------------------------------------------------------------
| Global patterns (ensure {issue}/{user}/{tag} are numeric)
|--------------------------------------------------------------------------
*/
Route::pattern('issue', '[0-9]+');
Route::pattern('user',  '[0-9]+');
Route::pattern('tag',   '[0-9]+');

/*
|--------------------------------------------------------------------------
| Landing
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => redirect()->route('projects.index'));

/*
|--------------------------------------------------------------------------
| Auth
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login',  [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.store');
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Core resources
|--------------------------------------------------------------------------
*/
Route::resource('projects', ProjectController::class);

// Issues nested under projects for create/store; shallow for show/edit/update/destroy
Route::resource('projects.issues', IssueController::class)->shallow();

// Global Issues index (navbar)
Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');

// Global create wizard (choose project) and proxy store
Route::get('/issues/create', function () {
    $projects = \App\Models\Project::orderBy('name')->get(['id','name']);
    return view('issues.choose-project', compact('projects'));
})->name('issues.create');
Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');

// Tags CRUD (list/create/edit as per your controllers/views)
Route::resource('tags', TagController::class);

/*
|--------------------------------------------------------------------------
| AJAX endpoints (auth required; policies enforce ownership)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // Comments (list + create)
    Route::get ('/issues/{issue}/comments', [IssueCommentController::class, 'index'])
        ->name('issues.comments.index');
    Route::post('/issues/{issue}/comments', [IssueCommentController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('issues.comments.store');

    // Tags attach/detach
    Route::post  ('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'attach'])
        ->name('issues.tags.attach');
    Route::delete('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'detach'])
        ->name('issues.tags.detach');

    // Assignees attach/detach (bonus)
    Route::post  ('/issues/{issue}/assignees/{user}', [IssueAssigneeController::class, 'attach'])
        ->name('issues.assignees.attach');
    Route::delete('/issues/{issue}/assignees/{user}', [IssueAssigneeController::class, 'detach'])
        ->name('issues.assignees.detach');
});

/*
|--------------------------------------------------------------------------
| Fallback
|--------------------------------------------------------------------------
*/
Route::fallback(fn () => redirect()->route('projects.index'));
