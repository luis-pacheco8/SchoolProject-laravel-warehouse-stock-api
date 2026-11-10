<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $fillable = ['name', 'sku', 'min_stock'];

    public function movements() {
        return $this->hasMany(Movement::class);
    }

    /**
     * 
     *
     * 
     */
    public function getTotalStockAttribute() {
        $entradas = $this->movements()->where('type', 'in')->sum('quantity');
        $saidas   = $this->movements()->where('type', 'out')->sum('quantity');
        $ajustes  = $this->movements()->where('type', 'adjust')->sum('quantity');

        return $entradas - $saidas + $ajustes;
    }
}