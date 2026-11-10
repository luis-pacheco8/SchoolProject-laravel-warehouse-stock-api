<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:in,out,transfer,adjust',
            'from_location_id' => 'required_if:type,out,transfer|exists:locations,id|nullable',
            'to_location_id' => 'required_if:type,in,transfer|exists:locations,id|nullable',
            'quantity' => 'required|integer|min:1',
            'justification' => 'required_if:type,adjust|string|max:255|nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'O produto é obrigatório.',
            'product_id.exists' => 'O produto selecionado não existe.',
            'type.required' => 'O tipo de movimento é obrigatório.',
            'type.in' => 'O tipo deve ser: in, out, transfer ou adjust.',
            'from_location_id.required_if' => 'A localização de origem é obrigatória para saídas e transferências.',
            'to_location_id.required_if' => 'A localização de destino é obrigatória para entradas e transferências.',
            'quantity.required' => 'A quantidade é obrigatória.',
            'quantity.min' => 'A quantidade tem de ser pelo menos 1.',
            'justification.required_if' => 'A justificação é obrigatória quando o tipo é um ajuste manual (adjust).',
        ];
    }
}