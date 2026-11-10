<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Movement;
use App\Http\Resources\ProductResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; // Necessário para as transações atómicas



class ProductController extends Controller
{
    /**
     * Rota: GET /api/products
     * Lista produtos com o stock total calculado e paginação por cursor.
     */
    public function index(Request $request)
    {
        $query = Product::query();

        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->cursorPaginate(10);

        return ProductResource::collection($products)->additional([
            'meta' => [
                'message' => 'Lista de produtos obtida com sucesso.'
            ]
        ]);
    }

    /**
     * Rota: POST /api/products
     * Regista um novo produto definindo o stock mínimo obrigatório.
     */
    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'sku'       => 'required|string|unique:products,sku',
            'min_stock' => 'required|integer|min:0',
        ]);

        $product = Product::create($data);

        return response()->json([
            'meta' => ['message' => 'Produto registado com sucesso.'],
            'data' => new ProductResource($product)
        ], 201);
    }

    /**
     * Rota: GET /api/alerts/low-stock
     * Lista todos os produtos cujo stock atual está abaixo do mínimo definido.
     */
    public function lowStock()
    {
        
        $products = Product::all()->filter(function ($product) {
            return $product->total_stock < $product->min_stock;
        });

        
        return response()->json([
            'meta' => [
                'message' => 'Alerta: Os produtos seguintes encontram-se abaixo do stock mínimo definido.'
            ],
            'data' => ProductResource::collection($products->values())
        ]);
    }

    /**
     * Rota: POST /api/products/{id}/adjust
     */
    public function adjust(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'quantity'      => 'required|integer|not_in:0',
            'justification' => 'required|string|max:500',
        ], [
            'quantity.not_in'          => 'A quantidade de ajuste não pode ser zero.',
            'justification.required'   => 'A justificação é estritamente obrigatória para efeitos de auditoria.',
        ]);

        
        return DB::transaction(function () use ($product, $data) {
            
            
            if ($product->total_stock + $data['quantity'] < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'A operação falhou. O ajuste resulta num stock negativo, o que não é permitido no armazém.'
                ]);
            }

            $movement = Movement::create([
                'product_id'    => $product->id,
                'user_id'       => Auth::id(), // Auditoria: quem fez o ajuste
                'type'          => 'adjust',
                'quantity'      => $data['quantity'],
                'justification' => $data['justification'],
            ]);

            return response()->json([
                'meta' => ['message' => 'Ajuste manual de stock registado na auditoria com sucesso.'],
                'data' => $movement->load('user', 'product')
            ], 201);
        });
    }
}