<?php declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return (bool) $this->user();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_public'  => (bool) $this->boolean('is_public'),
            'start_date' => $this->filled('start_date') ? $this->input('start_date') : null,
            'deadline'   => $this->filled('deadline') ? $this->input('deadline') : null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name'        => ['required','string','max:255'],
            'description' => ['nullable','string','max:20000'],
            'start_date'  => ['nullable','date'],
            'deadline'    => ['nullable','date','after_or_equal:start_date'],
            'is_public'   => ['boolean'],
        ];
    }
}
