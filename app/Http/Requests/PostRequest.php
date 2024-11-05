<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'excerpt' => 'required|min:50',
            'text' => 'required|min:50',
        ];
        if($this->isMethod('put')) {
            $rules['title'] = 'sometimes|string|max:255';
            $rules['author'] = 'sometimes|string|max:255';
            $rules['excerpt'] = 'sometimes|min:50';
            $rules['text'] = 'sometimes|min:50';
        }
        return $rules;
    }

    /**
     * Get the validation messages for response to request
     * 
     */
    public function messages()
    {
        return [
            'title.required' => 'O campo título é obrigatório.',
            'title.string' => 'O título deve ser uma string válida.',
            'title.max' => 'O título não pode exceder 255 caracteres.',
            'author.required' => 'O campo autor é obrigatório.',
            'author.string' => 'O autor deve ser uma string válida.',
            'excerpt.min' => 'O resumo deve ter no mínimo 50 caracteres.',
            'text.required' => 'O campo texto é obrigatório.',
            'text.min' => 'O texto deve ter no mínimo 50 caracteres.'
        ];
    }
}
