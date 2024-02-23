<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function list_customers()
    {
        $customers = Customer::with('user')->get();
        $customersWithUserData = $customers->map(function ($customer) {
            return [
                'id' => $customer->id,
                'user' => [
                    'user_id' => $customer->user->id,
                    'first_name' => $customer->user->first_name,
                    'last_name' => $customer->user->last_name,
                    'email' => $customer->user->email,
                ],
                'shipping_address' => $customer->shipping_address,
                'loyalty_points' => $customer->loyalty_points
            ];
        });

        return response()->json(['status' => 'success', 'data' => $customersWithUserData]);
    }

    public function show_customer($Id)
    {
        $customer = Customer::with('user')->find($Id);

        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found'], 404);
        }

        $customerData = [
            'status' => 'success',
            'data' => [
                'id' => $customer->id,
                'user' => [
                    'user_id' => $customer->user->id,
                    'first_name' => $customer->user->first_name,
                    'last_name' => $customer->user->last_name,
                    'email' => $customer->user->email,
                ],
                'shipping_address' => $customer->shipping_address,
                'loyalty_points' => $customer->loyalty_points
            ]
        ];

        return response()->json($customerData);
    }



    public function register_customer(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'nullable|string',
            'loyalty_points' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $customer = Customer::create($request->all());
        return response()->json(['status' => 'success', 'data' => $customer], 201);
    }

    public function update_customer(Request $request, $customerId)
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found'], 404);
        }

        $validator = Validator::make($request->all(), [
            'shipping_address' => 'nullable|string',
            'loyalty_points' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => $validator->errors()], 400);
        }

        $customer->update($request->all());
        return response()->json(['status' => 'success', 'data' => $customer]);
    }

    public function delete_customer($customerId)
    {
        $customer = Customer::find($customerId);
        if (!$customer) {
            return response()->json(['status' => 'error', 'message' => 'Customer not found'], 404);
        }

        $customer->delete();
        return response()->json(['status' => 'success', 'message' => 'Customer deleted']);
    }
}
