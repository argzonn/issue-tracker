<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueCommentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'author_name' => ['required','string','max:80'],
            'body'        => ['required','string','min:1','max:2000'],
        ];
    }
}
