<?php

namespace App\Http\Controllers\API;

use App\Models\product;
use Illuminate\Http\Request;
use function Pest\Laravel\json;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\StoreProductRequest;

class ProductController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $tenantName = $user->tenant->name;

        $products = product::where('tenant_id', $user->tenant_id)->get();
        return response()->json([
            'message' => "All products for tenant: $tenantName have been successfully returned",
            'tenant' => $tenantName,
            'products' => $products
        ]);
    }

    public function store(StoreProductRequest $request)
    {

        $product = product::create([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'stock_quantity' => $request->stock_quantity,
            'tenant_id' => auth()->user()->tenant_id,
        ]);

        return response()->json(
            [
                'message' => 'Product added successfully',
                'product' => $product
            ],
            201
        );
    }
    public function update(Request $request, $id)
    {
        $product = product::where('tenant_id', auth()->user()->tenant_id)->find($id);

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);

            Log::info('Raw Request Data:', $request->all());
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|numeric|min:0',
            'stock_quantity' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $product->update($request->only(['name', 'description', 'price', 'stock_quantity']));

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ]);
    }


    public function destroy($id)
    {
        $product = product::where('tenant_id', auth()->user()->tenant_id)->find($id);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
