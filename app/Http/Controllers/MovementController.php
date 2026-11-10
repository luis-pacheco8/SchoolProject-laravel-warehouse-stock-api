<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Product;
use App\Http\Requests\StoreMovementRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;



class MovementController extends Controller
{
    /**
     * Rota: POST /api/movements
     */
    public function store(StoreMovementRequest $request)
    {
        $data = $request->validated();
        $product = Product::findOrFail($data['product_id']);

        try {
            DB::beginTransaction();

            
            if ($data['type'] === 'transfer') {
                if ($data['from_location_id'] == $data['to_location_id']) {
                    return response()->json([
                        'errors' => ['A localização de origem e destino não podem ser iguais.']
                    ], 422);
                }
            }

            
            if ($data['type'] === 'out') {
                if ($product->total_stock - $data['quantity'] < 0) {
                    return response()->json([
                        'errors' => ['A operação falhou. A saída resulta num stock negativo, o que não é permitido no armazém.']
                    ], 422);
                }
            }

            $movement = Movement::create(array_merge($data, [
                'user_id' => Auth::id(),
            ]));

            DB::commit();

            $alert = null;
            if ($product->total_stock < $product->min_stock) {
                $alert = "Atenção: o stock de '{$product->name}' ({$product->total_stock} un.) está abaixo do mínimo definido ({$product->min_stock} un.).";
            }

            return response()->json([
                'meta' => [
                    'message' => 'Movimento registado com sucesso.',
                    'alert'   => $alert,
                ],
                'data' => $movement->load(['product', 'user', 'fromLocation', 'toLocation'])
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'errors' => ['Erro ao processar o movimento: ' . $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Rota: GET /api/movements
     */
    public function index(Request $request)
    {
        $query = Movement::with(['product', 'user', 'fromLocation', 'toLocation']);

        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $movements = $query->latest()->cursorPaginate(15);

        return response()->json([
            'meta' => ['message' => 'Histórico de movimentos obtido com sucesso.'],
            'data' => $movements
        ]);
    }

    /**
     * Rota: GET /api/reports/rotation
     * 
     */
    public function rotation(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to'   => 'required|date|after_or_equal:date_from',
            'limit'     => 'nullable|integer|min:1|max:100',
        ], [
            'date_from.required' => 'A data de início do período é obrigatória.',
            'date_to.required'   => 'A data de fim do período é obrigatória.',
            'date_to.after_or_equal' => 'A data de fim tem de ser igual ou posterior à data de início.',
        ]);

        $limit = $request->input('limit', 10);

        $result = Movement::query()
            ->whereIn('type', ['in', 'out', 'transfer'])
            ->whereDate('created_at', '>=', $request->date_from)
            ->whereDate('created_at', '<=', $request->date_to)
            ->select(
                'product_id',
                DB::raw('COUNT(*) as total_movements'),
                DB::raw('SUM(quantity) as total_quantity')
            )
            ->groupBy('product_id')
            ->orderByDesc('total_movements')
            ->with('product')
            ->limit($limit)
            ->get()
            ->map(function ($row) {
                return [
                    'product_id'      => $row->product_id,
                    'product_name'    => $row->product->name ?? 'N/A',
                    'sku'             => $row->product->sku ?? 'N/A',
                    'total_movements' => $row->total_movements,
                    'total_quantity'  => $row->total_quantity,
                ];
            });

        return response()->json([
            'meta' => [
                'message' => 'Relatório de rotatividade gerado com sucesso.',
                'period'  => [
                    'from' => $request->date_from,
                    'to'   => $request->date_to,
                ],
                'limit' => $limit,
            ],
            'data' => $result
        ]);
    }
}