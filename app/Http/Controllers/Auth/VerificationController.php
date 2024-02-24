<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;

class VerificationController extends Controller
{
    public function verify(Request $request, $userId, $hash)
    {
        $user = User::findOrFail($userId);

        if (! hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return response()->json(['status' => 'error', 'message' => 'Invalid verification link'], 400);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['status' => 'error', 'message' => 'Email already verified'], 400);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
            // If the user is verified, create a customer
            $this->createCustomer($user);

        }


        return response()->json(['status' => 'success', 'message' => 'Email verified successfully'], 200);
    }

    // The createCustomer method
    protected function createCustomer($user)
    {
        // Create a customer record for the newly verified user
        $customer = new Customer();
        $customer->user_id = $user->id;
        $customer->save();
    }

     // The createCustomer method
     protected function createVendor($user)
     {
        // Create the vendor
        $vendor = new Vendor();
        $vendor->user_id = $user->id;
        $vendor->company_name = $request->input('company_name');
        $vendor->company_address = $request->input('company_address');
        $vendor->category_id = $request->input('category_id');
        $vendor->save();
     }

    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return response()->json(['status' => 'error', 'message' => 'Email already verified'], 400);
        }

        $request->user()->sendEmailVerificationNotification();

        return response()->json(['status' => 'success', 'message' => 'Verification email resent'], 200);
    }
}
