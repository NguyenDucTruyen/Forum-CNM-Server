<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use App\Models\User;
use Carbon\Carbon;

class StripeController extends Controller
{
    public function createCheckoutSession(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        try {
            $checkoutSession = Session::create([
                'line_items' => [[
                    'price' => 'price_1QPqJfDRInUq1ZQePrzKuyM6',
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://localhost:5173/success?sessionId={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://localhost:5173/cancel',
            ]);

            return response()->json(['url' => $checkoutSession->url]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getSessionDetails(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $sessionId = $request->query('sessionId'); 
        try {
            $session = Session::retrieve($sessionId);

            if ($session && isset($session->customer_details->email)) {
                $email = $session->customer_details->email;

                $user = User::where('email', $email)->first();

                if ($user) {
                    $user->upgrade_at = Carbon::now();
                    $user->save();

                    return response()->json([
                        'message' => 'Upgrade successfully',
                        'data' => $user,
                    ]);
                } else {
                    return response()->json(['error' => 'Email not found, please check your email address'], 404);
                }
            } else {
                return response()->json(['error' => 'Invalid session details'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}