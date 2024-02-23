<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Vendor;

class VendorController extends Controller
{
    public function list_vendors()
    {
        $vendors = Vendor::with('user')->get();
        $vendorsWithUserData = $vendors->map(function ($vendor) {
            return [
                'id' => $vendor->id,
                'user' => [
                    'user_id' => $vendor->user->id,
                    'first_name' => $vendor->user->first_name,
                    'last_name' => $vendor->user->last_name,
                    'email' => $vendor->user->email,
                ],
                // Add other vendor attributes as needed
            ];
        });

        return response()->json(['status' => 'success', 'data' => $vendorsWithUserData]);
    }

    public function show_vendor($id)
    {
        $vendor = Vendor::with('user')->find($id);

        if (!$vendor) {
            return response()->json(['status' => 'error', 'message' => 'Vendor not found'], 404);
        }

        $vendorData = [
            'status' => 'success',
            'data' => [
                'id' => $vendor->id,
                'user' => [
                    'user_id' => $vendor->user->id,
                    'first_name' => $vendor->user->first_name,
                    'last_name' => $vendor->user->last_name,
                    'email' => $vendor->user->email,
                ],
                // Add other vendor attributes as needed
            ]
        ];

        return response()->json($vendorData);
    }

    public function register_vendor(Request $request)
    {
        $validator = Validator::make($request->all(), [
            // Define validation rules for vendor registration
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $vendor = Vendor::create($request->all());
        return response()->json(['status' => 'success', 'data' => $vendor], 201);
    }

    public function update_vendor(Request $request, $id)
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return response()->json(['status' => 'error', 'message' => 'Vendor not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            // Define validation rules for updating vendor
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $vendor->update($request->all());
        return response()->json(['status' => 'success', 'data' => $vendor]);
    }

    public function delete_vendor($id)
    {
        $vendor = Vendor::find($id);
        if (!$vendor) {
            return response()->json(['status' => 'error', 'message' => 'Vendor not found'], 404);
        }

        $vendor->delete();
        return response()->json(['status' => 'success', 'message' => 'Vendor deleted']);
    }
}
