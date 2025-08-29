use App\Http\Controllers\ProjectController;
use App\Http\Controllers\IssueController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\IssueTagController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('projects.index'));

Route::resource('projects', ProjectController::class);
Route::resource('issues', IssueController::class);
Route::resource('tags', TagController::class)->only(['index', 'store']);

// AJAX: comments (paginated list + store)
Route::get('/issues/{issue}/comments', [CommentController::class, 'index'])
    ->name('issues.comments.index');
Route::post('/issues/{issue}/comments', [CommentController::class, 'store'])
    ->name('issues.comments.store');

// AJAX: tags attach/detach on an issue
Route::post('/issues/{issue}/tags', [IssueTagController::class, 'attach'])
    ->name('issues.tags.attach');
Route::delete('/issues/{issue}/tags/{tag}', [IssueTagController::class, 'detach'])
    ->name('issues.tags.detach');
