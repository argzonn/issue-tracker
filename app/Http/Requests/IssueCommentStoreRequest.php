<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueCommentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        // If your policy is on Issue or Project, you can check here if needed
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'body' => ['required','string','min:1','max:5000'],
        ];
    }
}
