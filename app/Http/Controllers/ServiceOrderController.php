<?php

namespace App\Http\Controllers;

use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ServiceOrderController extends Controller
{
    public function index()
    {
        return response()->json(ServiceOrder::with('user')->get());
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehiclePlate' => 'required|string|size:7',
            'entryDateTime' => 'required|date',
            'exitDateTime' => 'nullable|date',
            'priceType' => 'nullable|string',
            'price' => 'required|numeric',
            'userId' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();
        $order = ServiceOrder::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Ordem criada com sucesso',
            'data' => $order,
        ], 200);
    }
}
