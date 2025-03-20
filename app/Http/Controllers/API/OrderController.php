<?php

namespace App\Http\Controllers\API;

use App\Models\order;
use App\Models\product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $orders = Order::where('tenant_id', $user->tenant_id)
            ->with('product')
            ->get();

        $tenantName = $user->tenant->name ?? 'Unknown Tenant';

        return response()->json([
            'message' => "All orders for tenant: $tenantName",
            'tenant_name' => $tenantName,
            'orders' => $orders->map(function ($order) {
                return [
                    'id' => $order->id,
                    'user_id' => $order->user_id,
                    'tenant_id' => $order->tenant_id,
                    'product' => [
                        'id' => $order->product->id,
                        'name' => $order->product->name,
                        'description' => $order->product->description,
                        'price' => $order->product->price,
                    ],
                    'quantity' => $order->quantity,
                    'total_price' => $order->total_price,
                    'status' => $order->status,
                    'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                ];
            })
        ]);
    }
    public function store(StoreOrderRequest $request)
    {

        $product = product::where('id', $request->product_id)
            ->where('tenant_id', auth()->user()->tenant_id)
            ->first();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        if ($product->stock_quantity < $request->quantity) {
            return response()->json(['error' => 'Insufficient stock'], 400);
        }

        $total_price = $product->price * $request->quantity;
        $product->decrement('stock_quantity', $request->quantity);

        $order = Order::create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'quantity' => $request->quantity,
            'total_price' => $total_price,
            'status' => 'pending',
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order
        ], 201);
    }

    public function destroy($id)
    {
        $order = Order::where('tenant_id', auth()->user()->tenant_id)->find($id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(['message' => 'Order canceled successfully']);
    }
}
