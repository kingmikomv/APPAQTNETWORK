<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Xendit\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{
    /**
     * Handle checkout process and create an invoice.
     *
     * @return \Illuminate\View\View
     */
    public function checkout()
    {
        try {
            // Initialize Xendit API key
            //Xendit::setApiKey(config('xendit.secret_key'));
           

            Configuration::setXenditKey('XENDIT_API_KEY');
            // Create an invoice
            $invoice = Invoice::create([
                'external_id' => 'invoice-' . time(),
                'amount' => 150000, // Payment amount in IDR
                'payer_email' => 'customer@example.com',
                'description' => 'Purchase of Product X',
            ]);

            // Pass the invoice data to the view
            return view('checkout', ['invoice' => $invoice]);
        } catch (\Exception $e) {
            // Log error and handle exception
            Log::error('Error creating invoice: ' . $e->getMessage());
            return redirect()->back()->withErrors('Failed to create invoice. Please try again.');
        }
    }

    /**
     * Handle webhook notifications from Xendit.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function webhook(Request $request)
    {
        // Get the signature header from the request
        $signature = $request->header('x-xendit-signature');

        try {
            // Validate the webhook signature
            $isValid = Configuration::validateCallback($request->getContent(), $signature);

            if ($isValid) {
                // Process the webhook payload
                $payload = $request->all();

                // Example: Log the payment details
                Log::info('Webhook received successfully', $payload);

                // TODO: Update payment status in the database or other actions

                return response()->json(['message' => 'Webhook processed successfully.'], 200);
            } else {
                // Invalid signature
                Log::warning('Invalid webhook signature received.');
                return response()->json(['message' => 'Invalid signature.'], 400);
            }
        } catch (\Exception $e) {
            // Handle errors and log the exception
            Log::error('Error processing webhook: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to process webhook.'], 500);
        }
    }
}
