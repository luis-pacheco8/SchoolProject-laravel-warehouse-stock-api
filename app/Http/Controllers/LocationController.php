<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\Request;



class LocationController extends Controller
{
    /**
     * Rota: GET /api/locations
     * Devolve a lista de todas as localizações registadas no armazém.
     */
    public function index()
    {
        $locations = Location::all();

        return response()->json([
            'meta' => ['message' => 'Lista de localizações do armazém obtida com sucesso.'],
            'data' => $locations
        ]);
    }

    /**
     * Rota: GET /api/locations/{id}/products
     * Lista os produtos e a respetiva quantidade disponíveis numa localização específica.
     */
    public function products($id)
    {
        $location = Location::findOrFail($id);

        
        $products = Product::with('movements')->whereHas('movements', function ($query) use ($id) {
            $query->where('to_location_id', $id)
                  ->orWhere('from_location_id', $id);
        })->get()->map(function ($product) use ($id) {

            
            $in = $product->movements
                ->whereIn('type', ['in', 'transfer'])
                ->where('to_location_id', $id)
                ->sum('quantity');

            
            $out = $product->movements
                ->whereIn('type', ['out', 'transfer'])
                ->where('from_location_id', $id)
                ->sum('quantity');

            $current_stock = $in - $out;

            return [
                'id'                => $product->id,
                'name'              => $product->name,
                'sku'               => $product->sku,
                'stock_in_location' => $current_stock,
            ];
        })->filter(function ($product) {
            
            return $product['stock_in_location'] > 0;
        });

        return response()->json([
            'meta' => ['message' => 'Produtos atualmente na localização: ' . $location->name],
            'data' => $products->values()
        ]);
    }
}