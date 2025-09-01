<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\IssueAssigneeController;
use App\Http\Controllers\IssueCommentController;
use App\Http\Controllers\IssueSearchController;

// ------------------- Auth -------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
});
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ------------------- Home -------------------
Route::get('/', fn () => redirect()->route('projects.index'));

// ------------------- Projects -------------------
Route::resource('projects', ProjectController::class);

// ------------------- Issues -------------------
Route::resource('issues', IssueController::class);

// ------------------- Tags -------------------
Route::resource('tags', TagController::class)
    ->only(['index','store','edit','update','destroy']);

// ------------------- Comments (AJAX) -------------------
Route::get('/issues/{issue}/comments', [IssueCommentController::class, 'index'])
    ->name('issues.comments.index');
Route::post('/issues/{issue}/comments', [IssueCommentController::class, 'store'])
    ->name('issues.comments.store');

// ------------------- Tags attach/detach (AJAX) -------------------
Route::post('/issues/{issue}/tags', [IssueTagController::class, 'store'])
    ->name('issues.tags.store');
Route::delete('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'destroy'])
    ->name('issues.tags.destroy');

// ------------------- Assignees attach/detach (AJAX) -------------------
Route::post('/issues/{issue}/assignees', [IssueAssigneeController::class, 'store'])
    ->name('issues.assignees.store');
Route::delete('/issues/{issue}/assignees/{user}', [IssueAssigneeController::class, 'destroy'])
    ->name('issues.assignees.destroy');

// ------------------- Issue Search (AJAX debounce) -------------------
Route::get('/issues/search', IssueSearchController::class)
    ->name('issues.search');
