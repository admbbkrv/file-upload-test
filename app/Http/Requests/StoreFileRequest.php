<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->merge([
            'chunkIndex' => (int) $this->chunkIndex,
            'totalChunks' => (int) $this->totalChunks,
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'file' => ['required', 'file'],
            'fileName' => ['required', 'string'],
            'chunkIndex' => ['required', 'int', 'gte:0', 'lt:totalChunks'],
            'totalChunks' => ['required', 'int', 'gt:0', 'gt:chunkIndex'],
        ];
    }
}
