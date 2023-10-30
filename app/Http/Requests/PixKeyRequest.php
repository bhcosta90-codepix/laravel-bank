<?php

namespace App\Http\Requests;

use App\Models\Account;
use CodePix\Bank\Domain\Enum\EnumPixType;
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

    protected function prepareForValidation(): void
    {
        $this->merge([
            'account' => $account = request()->route()->parameter('account'),
        ]);

        if(request('kind') == EnumPixType::DOCUMENT->value){
            $this->merge([
                'key' => Account::find($account)->document,
            ]);
        }
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
