<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

class IssueSearchController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $issues = Issue::query()
            ->forList()
            ->status($request->string('status')->toString())
            ->priority($request->string('priority')->toString())
            ->tag($request->integer('tag'))
            ->keyword($request->string('q')->toString())
            ->latest()
            ->paginate(15);

        $html = View::make('issues._list', ['issues'=>$issues])->render();

        return response()->json(['ok'=>true,'html'=>$html]);
    }
}
