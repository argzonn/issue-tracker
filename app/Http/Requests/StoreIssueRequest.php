<?php declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Issue;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIssueRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = Project::query()
            ->select('id', 'owner_id', 'is_public')
            ->find((int) $this->input('project_id'));

        return $project && $this->user()?->can('update', $project);
    }

    protected function prepareForValidation(): void
    {
        $norm = static function (?string $v): ?string {
            if ($v === null) return null;
            $v = strtolower($v);
            // turn "in progress" -> "in_progress"
            return str_replace(' ', '_', $v);
        };

        $this->merge([
            'project_id' => (int) $this->input('project_id'),
            'status'     => $norm($this->input('status', 'open')),
            'priority'   => $norm($this->input('priority', 'medium')),
            'due_date'   => $this->filled('due_date') ? $this->input('due_date') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'project_id'  => ['required', 'integer', 'exists:projects,id'],
            'title'       => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'max:20000'],
            'status'      => ['required', Rule::in(Issue::STATUSES)],
            'priority'    => ['required', Rule::in(Issue::PRIORITIES)],
            'due_date'    => ['nullable', 'date'],
        ];
    }
}
