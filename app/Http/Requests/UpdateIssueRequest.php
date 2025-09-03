<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\IssuePriority;
use App\Domain\IssueStatus;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdateIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user  = $this->user();
        $issue = $this->route('issue'); // \App\Models\Issue

        if (!$user || !$issue) return false;

        $ok = $user->can('update', $issue->project);

        if ($this->filled('project_id') && (int)$this->input('project_id') !== (int)$issue->project_id) {
            $target = Project::query()->select('id','owner_id')->find((int)$this->input('project_id'));
            $ok = $ok && $target && $user->can('update', $target);
        }

        return (bool) $ok;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'project_id' => $this->filled('project_id') ? (int) $this->input('project_id') : null,
            'status'     => $this->filled('status')   ? strtolower((string)$this->input('status'))   : null,
            'priority'   => $this->filled('priority') ? strtolower((string)$this->input('priority')) : null,
            'due_date'   => $this->filled('due_date') ? $this->input('due_date') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'project_id'  => ['sometimes','integer','exists:projects,id'],
            'title'       => ['sometimes','string','min:3','max:255'],
            'description' => ['sometimes','nullable','string','max:20000'],
            'status'      => ['sometimes', new Enum(IssueStatus::class)],
            'priority'    => ['sometimes', new Enum(IssuePriority::class)],
            'due_date'    => ['sometimes','nullable','date'],
        ];
    }
}
