<?php

namespace App\Http\Requests;

use App\Models\ProductMedia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // El product_id normalmente llega por ruta; se valida por si viene en el body.
            'product_id' => ['sometimes', 'integer', 'exists:products,id'],
            'type' => ['sometimes', 'string', Rule::in([ProductMedia::TYPE_IMAGE])],
            'path' => ['required', 'string'],
            'sort_order' => ['sometimes', 'integer', 'min:1'],
            'is_primary' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'path.required' => 'El path del archivo es obligatorio.',
            'type.in' => 'El tipo de contenido no es válido.',
        ];
    }
}
