<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Customer;

/**
 * @OA\Tag(
 *     name="Customers",
 *     description="API Endpoints for managing customers"
 * )
 */
class CustomerController extends Controller
{
    /**
     * List all customers.
     *
     * @OA\Get(
     *     path="/api/customers",
     *     summary="List all customers",
     *     tags={"Customers"},
     *     @OA\Response(
     *         response=200,
     *         description="List of customers",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Customer")
     *         )
     *     )
     * )
     *
     * @return \Illuminate\Http\JsonResponse
     */
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

    /**
     * Retrieve a customer by ID.
     *
     * @OA\Get(
     *     path="/api/customers/{id}",
     *     summary="Retrieve a customer by ID",
     *     tags={"Customers"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Customer ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Customer details",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Customer not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Customer not found")
     *         )
     *     )
     * )
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show_customer($id)
    {
        $customer = Customer::with('user')->find($id);

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

    /**
     * Register a new customer.
     *
     * @OA\Post(
     *     path="/api/customers",
     *     summary="Register a new customer",
     *     tags={"Customers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/CustomerRegistrationRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Customer registered successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Customer")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="object", example={"shipping_address":["The shipping address field is required."]})
     *         )
     *     )
     * )
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
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

    // Other methods omitted for brevity
}
