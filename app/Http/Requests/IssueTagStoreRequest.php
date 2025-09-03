<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueTagStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $issue = $this->route('issue');
        return $issue && $this->user()?->can('update', $issue->project);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['tag_id' => (int) $this->input('tag_id')]);
    }

    public function rules(): array
    {
        return [
            'tag_id' => ['required','integer','exists:tags,id'],
        ];
    }
}
