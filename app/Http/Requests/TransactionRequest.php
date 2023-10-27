<?php

namespace App\Http\Requests;

use CodePix\Bank\Domain\Enum\EnumPixType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class TransactionRequest extends FormRequest
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
            'value' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'min:3', 'max:100'],
            'account' => ['required', 'uuid', 'exists:accounts,id'],
            'kind' => ['required', new Enum(EnumPixType::class)],
            'key' => ['required'],
        ];

        match ($this->get('kind')) {
            'id' => $rules['key'] = array_merge($rules['key'], ['uuid']),
            'email' => $rules['key'] = array_merge($rules['key'], ['email']),
            'phone' => $rules['key'] = array_merge($rules['key'], ['celular_com_ddd']),
            'document' => $rules['key'] = array_merge($rules['key'], ['cpf_ou_cnpj']),
            default => null,
        };

        return $rules;
    }
}
