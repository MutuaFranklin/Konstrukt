<?php

namespace App\Http\Controllers\Resource;

use App\Http\Controllers\Controller;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Request;
use App\Models\Order;


class OrderController extends Controller
{
       // Get all orders
       public function all_orders()
       {
           $orders = Order::with(['customer', 'product'])->get();
           return response()->json(['status' => 'success', 'data' => $orders]);
       }

       public function create_new_order(Request $request)
        {
            try {
                $request->validate([
                    'customer_id' => 'required|exists:customers,id',
                    'product_id' => 'required|exists:products,id',
                    'quantity' => 'required|integer|min:1',
                    'total_price' => 'required|numeric|min:0',
                ]);

                $order = Order::create($request->all());
                return response()->json(['status' => 'success', 'data' => $order], 201);
            } catch (ValidationException $e) {
                return response()->json(['status' => 'error', 'message' => $e->validator->errors()->first()], 422);
            } catch (\Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }
            // Get a single order
       public function show_order($id)
       {
           $order = Order::with(['customer', 'product'])->find($id);
           if (!$order) {
               return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
           }
           return response()->json(['status' => 'success', 'data' => $order]);
       }

       // Update an order
       public function update_order(Request $request, $id)
       {
           $order = Order::find($id);
           if (!$order) {
               return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
           }

           $request->validate([
               'customer_id' => 'required|exists:customers,id',
               'product_id' => 'required|exists:products,id',
               'quantity' => 'required|integer|min:1',
               'total_price' => 'required|numeric|min:0',
           ]);

           $order->update($request->all());
           return response()->json(['status' => 'success', 'data' => $order]);
       }

       // Delete an order
       public function delete_order($id)
       {
           $order = Order::find($id);
           if (!$order) {
               return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
           }

           $order->delete();
           return response()->json(['status' => 'success', 'message' => 'Order deleted successfully']);
       }
}
