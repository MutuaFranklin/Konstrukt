<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // Get all products
    public function list_products()
    {
        $products = Product::with('vendor')->get();
        return response()->json(['status' => 'success', 'data' => $products]);
    }

    // Create a new product
    public function create_new_product(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'description' => 'required',
                'price' => 'required|numeric',
                'vendor_id' => 'required|exists:vendors,id',
                'stock_quantity' => 'required|integer'
            ]);

            $product = Product::create($request->all());
            return response()->json(['status' => 'success', 'data' => $product], 201);
        } catch (ValidationException $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], $e->status);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Get a single product
    public function show_product($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    // Update a product
    public function update_product(Request $request, $id)
    {
        try {
            $product = Product::findOrFail($id);

            $request->validate([
                'name' => 'sometimes|required',
                'description' => 'sometimes|required',
                'price' => 'sometimes|required|numeric',
                'vendor_id' => 'sometimes|required|exists:vendors,id',
                'stock_quantity' => 'sometimes|required|integer'
            ]);

            $updateData = array_filter($request->only(['name', 'description', 'price', 'vendor_id', 'stock_quantity']));

            $product->update($updateData);
            return response()->json(['status' => 'success', 'data' => $product], 200);
        } catch (ValidationException $e) {
            $failedFields = collect($e->errors())->keys()->implode(', ');
            return response()->json(['status' => 'error', 'message' => 'Validation failed for fields: ' . $failedFields], $e->status);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    // Delete a product
    public function delete_product($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(null, 204);
    }
}
