<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function createIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|integer|min:1',
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => $request->amount,
            'currency' => 'aed',
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        return response()->json([
            'status' => true,
            'data' => [
                'clientSecret' => $intent->client_secret,
            ],
        ]);
    }
}
