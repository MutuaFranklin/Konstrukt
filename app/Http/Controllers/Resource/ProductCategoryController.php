<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProductCategory;

class ProductCategoryController extends Controller
{
    public function all_categories()
    {
        $categories = ProductCategory::all();
        return response()->json(['categories' => $categories], 200);
    }

    public function show_category($id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json(['category' => $category], 200);
    }

    public function create_category(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:product_categories,name',
        ]);

        $category = ProductCategory::create([
            'name' => $request->input('name'),
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

  public function update_category(Request $request, $id)
{
    try {
        $category = ProductCategory::findOrFail($id);

        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            // Add more validation rules as needed
        ]);

        // Check if the request fails validation
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Update the category
        $category->update($request->all());

        return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
    } catch (\Exception $e) {
        // Log the exception for further investigation
        Log::error('Error updating category: ' . $e->getMessage());

        // Return a generic error message
        return response()->json(['message' => 'An error occurred while updating the category'], 500);
    }
}


    public function delete_category($id)
    {
        $category = ProductCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
