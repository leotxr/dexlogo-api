<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
        return [
            'nome'      => 'required|string',
            'email'     => 'required|string|email',
            'telefone'  => 'required|string|max:11',
            'plano'     => 'required|string|in:basico,pro',
        ];
    }


    public function messages(): array
    {
        return [
            'nome.required'                 => 'O nome é obrigatório.',
            'email.required'                => 'O e-mail é obrigatório',
            'telefone.required'             => 'O telefone é obrigatório',
            'plano.required'                => 'É obrigatório informar um plano',

        ];
    }
}
