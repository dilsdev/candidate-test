<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportSupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:json,txt', 'max:10240'],
            'strategy' => ['required_without:manual_resolve', 'in:overwrite,skip,duplicate,reject,manual'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Please select a JSON file to import.',
            'file.mimes' => 'The file must be a JSON file.',
            'strategy.required_without' => 'Please select a conflict resolution strategy.',
        ];
    }
}
