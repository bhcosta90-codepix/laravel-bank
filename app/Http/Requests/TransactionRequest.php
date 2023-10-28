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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'account' => request()->route()->parameter('account'),
        ]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'account' => ['required', 'uuid', 'exists:accounts,id'],
            'value' => ['required', 'numeric', 'min:0.01'],
            'description' => ['required', 'string', 'min:3', 'max:100'],
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
