<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueTagController;
use App\Http\Controllers\IssueMemberController;
use Illuminate\Support\Facades\Route;

// ------------------- Auth -------------------
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Home
Route::get('/', fn () => redirect()->route('projects.index'));

// Projects (controller enforces auth on create/edit/delete)
Route::resource('projects', ProjectController::class);

// Issues
Route::resource('issues', IssueController::class);

// Tags
Route::resource('tags', TagController::class)->only(['index','store','edit','update','destroy']);

// Comments (AJAX)
Route::get('/issues/{issue}/comments', [CommentController::class, 'index'])->name('issues.comments.index');
Route::post('/issues/{issue}/comments', [CommentController::class, 'store'])->name('issues.comments.store');

// Tags attach/detach (AJAX)
Route::post('/issues/{issue}/tags', [IssueTagController::class, 'attach'])->name('issues.tags.attach');
Route::delete('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'detach'])->name('issues.tags.detach');

// Assignees attach/detach (AJAX)
Route::post('/issues/{issue}/assignees', [IssueMemberController::class, 'attach'])->name('issues.assignees.attach');
Route::delete('/issues/{issue}/assignees/{user}', [IssueMemberController::class, 'detach'])->name('issues.assignees.detach');
