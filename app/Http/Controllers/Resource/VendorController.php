<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
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
            ]
        ];

        return response()->json($vendorData);
    }

    public function register_vendor(Request $request)
    {
         // Check if the user already exists
        $user = User::where('email', $request->input('email'))->first();


        // If user doesn't exist, create a new user using the register method from RegisterController
        if (!$user) {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
                'company_name' => 'required|string|max:255',
                'company_address' => 'required|string|max:255',
                'category_id' => 'required|integer|exists:vendor_categories,id',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
            }

             // Create a new user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

        }

       // Check if the vendor already exists
        $company = Vendor::where('company_name', $request->input('company_name'))->first();

        if ($company) {
            // Company already exists, return an error response
            return response()->json(['status' => 'error', 'message' => 'This company already exists and cannot be registered twice.'], 400);
        } else {
            // Company doesn't exist, proceed with vendor creation
            if ($user) {
                 // Validate the request
                 $validator = Validator::make($request->only(['company_name', 'company_address', 'category_id']), [
                    'company_name' => 'required|string|max:255',
                    'company_address' => 'required|string|max:255',
                    'category_id' => 'required|integer|exists:vendor_categories,id',
                ]);

                // Create the vendor with the user ID
                $vendor = new Vendor();
                $vendor->user_id = $user->id;  // Assign the user_id
                $vendor->company_name = $request->input('company_name');
                $vendor->company_address = $request->input('company_address');
                $vendor->category_id = $request->input('category_id');
                $vendor->save();
                if ($validator->fails()) {
                    return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
                }

                if ($user->hasVerifiedEmail()) {
                    // Return success response with vendor data
                    return response()->json(['status' => 'success', 'data' => $vendor], 201);
                }else{
                    // Dispatch email verification notification
                    $user->sendEmailVerificationNotification();
                    // Return JSON response with success message
                    return response()->json(['status' => 'success', 'message' => 'Registration successful! Please verify your email.']);
                }

            }
        }


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
