<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name')) {
            // trim and collapse multiple spaces to one
            $this->merge([
                'name' => trim(preg_replace('/\s+/', ' ', (string) $this->input('name'))),
            ]);
        }
    }

    public function rules(): array
    {
        // If updating, ignore current tag ID in unique rule
        $currentTagId = optional($this->route('tag'))->id;

        return [
            'name' => [
                'required',
                'string',
                'max:50',
                Rule::unique('tags', 'name')->ignore($currentTagId),
            ],
            'color' => [
                'nullable',
                // accepts 3- or 6-digit hex, with or without #
                'regex:/^#?[0-9A-Fa-f]{3}([0-9A-Fa-f]{3})?$/',
            ],
        ];
    }
}
