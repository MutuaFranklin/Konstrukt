<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;
use App\Http\Controllers\Auth\RegisterController;
use App\Models\User;
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
                'company_name' => $vendor->company_name,
                'company_address' => $vendor->company_address,
                'category_name' => $vendor->category->name
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
                'company_name' => $vendor->company_name,
                'company_address' => $vendor->company_address,
                'category_id' => $vendor->category_id
                // Add other vendor attributes as needed
            ]
        ];

        return response()->json($vendorData);
    }

    public function register_vendor(Request $request)
    {
       // Validate the request
        $validator = Validator::make($request->all(), [
            'company_name' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
            'category_id' => 'required|integer|exists:vendor_categories,id', // Assuming categories are stored in a table named "categories"
            // Add other validation rules for additional vendor fields here
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        // Check if the user already exists
        $user = User::where('email', $request->input('email'))->first();

        // If user doesn't exist, create a new user using the register method from RegisterController
        if (!$user) {
            // Call the register method from the RegisterController
            $registerController = App::make(RegisterController::class);
            $response = $registerController->register($request);

            // Check if the registration was successful
            if ($response->getStatusCode() !== 201) {
                // Return the registration error response
                return $response;
            }

            // Extract the created user from the response
            $user = json_decode($response->getContent())->original->user;
        }


           // If user exists and is verified, create the vendor
        if ($user && $user->markEmailAsVerified()) {
            // Create the vendor with the user ID
            $vendor = new Vendor();
            $vendor->user_id = $user->id;  // Assign the user_id
            $vendor->company_name = $request->input('company_name');
            $vendor->company_address = $request->input('company_address');
            $vendor->category_id = $request->input('category_id');
            $vendor->save();

            // Return success response with vendor data
            return response()->json(['status' => 'success', 'data' => $vendor], 201);
        }

        // If user doesn't exist or is not verified, return error response
        return response()->json(['status' => 'error', 'message' => 'User not found or not verified'], 404);
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
