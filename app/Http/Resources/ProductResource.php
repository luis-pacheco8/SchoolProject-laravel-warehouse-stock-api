<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transforma o recurso num array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // Identificação básica
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            
            // O stock em tempo real calculado dinamicamente pelo modelo
            'current_stock' => $this->total_stock,
            'minimum_stock' => $this->min_stock,
            
            // Indicador rápido se está abaixo do stock mínimo
            'is_low_stock' => $this->total_stock < $this->min_stock,
        ];
    }
}