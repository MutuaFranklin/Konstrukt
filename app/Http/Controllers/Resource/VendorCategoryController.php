<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorCategory;

class VendorCategoryController extends Controller
{
    public function all_categories()
    {
        $categories = VendorCategory::all();
        return response()->json(['categories' => $categories], 200);
    }

    public function show_category($id)
    {
        $category = VendorCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        return response()->json(['category' => $category], 200);
    }

    public function create_category(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|string|unique:vendor_categories,name',
        ]);

        $category = VendorCategory::create([
            'name' => $request->input('name'),
        ]);

        return response()->json(['message' => 'Category created successfully', 'category' => $category], 201);
    }

    public function update_category(Request $request, $id)
    {
        $category = VendorCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->update($request->all());

        return response()->json(['message' => 'Category updated successfully', 'category' => $category], 200);
    }

    public function delete_category($id)
    {
        $category = VendorCategory::find($id);

        if (!$category) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully'], 200);
    }
}
