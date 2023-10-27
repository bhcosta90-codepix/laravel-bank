<?php

namespace App\Http\Requests;

use CodePix\System\Domain\Enum\EnumPixType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class PixKeyRequest extends FormRequest
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
            'bank' => ['required', 'uuid'],
            'kind' => ['required', new Enum(EnumPixType::class)],
        ];

        match ($this->get('kind')) {
            'email' => $rules['key'] = ['required', 'email'],
            'phone' => $rules['key'] = ['required', 'celular_com_ddd'],
            'document' => $rules['key'] = ['required', 'cpf_ou_cnpj'],
            default => null,
        };

        return $rules;
    }
}
