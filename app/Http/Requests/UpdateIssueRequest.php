<?php
namespace App\Http\Requests;

class UpdateIssueRequest extends StoreIssueRequest {

    public function rules(): array
{
    return [
        'name'        => ['required','string','max:255'],
        'description' => ['nullable','string'],
        'start_date'  => ['nullable','date'],
        'deadline'    => ['nullable','date','after_or_equal:start_date'],
        'is_public'   => ['sometimes','boolean'],
    ];
}
}
