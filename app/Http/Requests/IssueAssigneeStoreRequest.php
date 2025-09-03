<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueAssigneeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        $issue = $this->route('issue');
        return $issue && $this->user()?->can('update', $issue->project);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['user_id' => (int) $this->input('user_id')]);
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required','integer','exists:users,id'],
        ];
    }
}
