<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\{
    ProjectController,
    IssueController,
    TagController,
    IssueCommentController,
    IssueTagController,
    IssueAssigneeController
};
use App\Models\Project;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Clean, conflict-free routes. AJAX endpoints return JSON.
*/

// Force numeric IDs so "create" never matches {issue}/{user}/{tag}
Route::pattern('issue', '[0-9]+');
Route::pattern('user',  '[0-9]+');
Route::pattern('tag',   '[0-9]+');

// Landing
Route::get('/', fn () => redirect()->route('projects.index'));

// --- Minimal auth stubs so layout links resolve ---
Route::get('/login', function () {
    return redirect()->route('projects.index');
})->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('projects.index');
})->name('logout');

// ------------------- Core resources -------------------
Route::resource('projects', ProjectController::class);

// Issues are nested for create/store; shallow for show/edit/update/destroy
Route::resource('projects.issues', IssueController::class)->shallow();

// Global Issues index (navbar) + top-level create wizard + store proxy
Route::get('/issues', [IssueController::class, 'index'])->name('issues.index');

Route::get('/issues/create', function (Request $request) {
    // choose-project page already exists in your repo
    $projects = Project::orderBy('name')->get(['id','name']);
    return view('issues.choose-project', compact('projects'));
})->name('issues.create');

// Proxy store for the /issues/create page (form posts here)
Route::post('/issues', [IssueController::class, 'store'])->name('issues.store');

// Tags: full resource (your view uses edit/update/destroy)
Route::resource('tags', TagController::class);

Route::middleware('auth')->group(function () {
    Route::resource('issues', IssueController::class);
    Route::get('issues/{issue}/comments', [IssueCommentController::class, 'index'])
        ->name('issues.comments.index');
    Route::post('issues/{issue}/comments', [IssueCommentController::class, 'store'])
        ->name('issues.comments.store');
});

// ------------------- AJAX: Comments (list + create) -------------------
Route::get   ('/issues/{issue}/comments', [IssueCommentController::class, 'index'])
    ->name('issues.comments.index');

Route::post  ('/issues/{issue}/comments', [IssueCommentController::class, 'store'])
    ->middleware('throttle:6,1')
    ->name('issues.comments.store');

// ------------------- AJAX: Tags attach/detach -------------------
Route::post  ('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'attach'])
    ->name('issues.tags.attach');

Route::delete('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'detach'])
    ->name('issues.tags.detach');

// ------------------- AJAX: Assignees attach/detach -------------------
Route::post  ('/issues/{issue}/assignees/{user}', [IssueAssigneeController::class, 'attach'])
    ->name('issues.assignees.attach');

Route::delete('/issues/{issue}/assignees/{user}', [IssueAssigneeController::class, 'detach'])
    ->name('issues.assignees.detach');

// ------------------- Fallback -------------------
Route::fallback(function () {
    return redirect()->route('projects.index');
});
