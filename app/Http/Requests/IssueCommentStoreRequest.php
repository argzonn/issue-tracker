<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueCommentStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
<<<<<<< HEAD
        return true;
=======
        return auth()->check();
>>>>>>> fix/comments-ajax
    }

    public function rules(): array
    {
        return [
<<<<<<< HEAD
            'author_name' => ['required', 'string', 'max:100'],
            'body'        => ['required', 'string', 'max:2000'],
=======
            'author_name' => ['required','string','max:80'],
            'body'        => ['required','string','min:1','max:2000'],
>>>>>>> fix/comments-ajax
        ];
    }
}
