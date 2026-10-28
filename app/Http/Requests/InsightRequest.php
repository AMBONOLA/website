<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InsightRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        if (blank($this->input('slug'))) {
            $this->merge(['slug' => Str::slug($this->string('title')->toString())]);
        }
    }

    /** @return array<string, ValidationRule|array<mixed>|string> */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('insights')->ignore($this->route('insight')),
            ],
            'excerpt' => ['required', 'string', 'max:300'],
            'body' => ['required', 'string'],
            'is_published' => ['required', 'boolean'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and single dashes.',
        ];
    }
}
