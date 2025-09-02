<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project'); // \App\Models\Project
        return $project && $this->user()?->can('update', $project);
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_public'  => $this->has('is_public') ? (bool) $this->boolean('is_public') : null,
            'start_date' => $this->filled('start_date') ? $this->input('start_date') : null,
            'deadline'   => $this->filled('deadline') ? $this->input('deadline') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'        => ['sometimes','string','max:255'],
            'description' => ['sometimes','nullable','string','max:20000'],
            'start_date'  => ['sometimes','nullable','date'],
            'deadline'    => ['sometimes','nullable','date','after_or_equal:start_date'],
            'is_public'   => ['sometimes','boolean'],
        ];
    }
}
