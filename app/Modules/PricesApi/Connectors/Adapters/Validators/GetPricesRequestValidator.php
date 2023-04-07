<?php

namespace App\Modules\PricesApi\Connectors\Adapters\Validators;

use Illuminate\Foundation\Http\FormRequest;

class GetPricesRequestValidator extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'account_reference' => 'sometimes|required|string|min:1',
            'products_skus' => 'required|array',
            'products_skus.*' => 'required|string',
        ];
    }
}