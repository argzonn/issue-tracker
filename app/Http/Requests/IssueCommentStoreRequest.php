<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueCommentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $issue = $this->route('issue'); // \App\Models\Issue
        return (bool) $this->user()?->can('view', $issue);
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required','string','max:100'],
            'body'        => ['required','string','max:2000'],
        ];
    }
}
