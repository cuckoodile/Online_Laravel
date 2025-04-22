<?php

namespace App\Http\Controllers;

use App\Models\TransactionPaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionPaymentMethodController extends Controller
{
    /**
     * Display a listing of all payment methods.
     */
    public function index() 
    {
        $methods = TransactionPaymentMethod::all();
        
        return $this->Ok($methods);
    }

    /**
     * Display the specified payment method.
     */
    public function show(string $id)
    {
        $method = TransactionPaymentMethod::find($id);
        
        if (empty($method)) {
            return $this->NotFound("Transaction Payment Method not found");
        }

        $method->transactions;

        return $this->Ok($method);
    }

    /**
     * Store a newly created payment method.
     */
    public function store(Request $request)
    {
        // Define default payment methods first
        $defaultMethods = ['Cash On Delivery', 'Gcash', 'Debit Cards', 'Credit Cards', 'Digital Wallet'];

        $inputs = $request->all();
        $inputs["name"] = $this->SanitizedName($inputs["name"]);

        // Apply validation with a custom message
        $validator = validator()->make($inputs, [
            "name" => [
                "required",
                "string",
                function ($attribute, $value, $fail) use ($defaultMethods) {
                    if (!in_array($value, $defaultMethods)) {
                        $fail("Invalid Method! Please choose only the available methods: " . implode(", ", $defaultMethods));
                    }
                }
            ]
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        // Ensure default payment methods exist in the database
        foreach ($defaultMethods as $method) {
            if (!TransactionPaymentMethod::where('name', $method)->exists()) {
                TransactionPaymentMethod::create(['name' => $method]);
            }
        }

        $method = TransactionPaymentMethod::create($validator->validated());

        return $this->Created($method, "Transaction Payment Method has been created");
    }


    /**
     * Update the specified payment method.
     */
    public function update(Request $request, string $id)
    {
        // Define default payment methods first
        $defaultMethods = ['Cash On Delivery', 'Gcash', 'Debit Cards', 'Credit Cards', 'Digital Wallet'];
    
        $inputs = $request->all();
        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");
    
        // Find the payment method before validation
        $method = TransactionPaymentMethod::find($id);
    
        if (empty($method)) {
            return $this->NotFound("Transaction Payment Method not found");
        }
    
        // Apply validation with a custom message
        $validator = validator()->make($inputs, [
            "name" => [
                "required",
                "string",
                function ($attribute, $value, $fail) use ($defaultMethods) {
                    if (!in_array($value, $defaultMethods)) {
                        $fail("Invalid Method! Please choose only the available methods: " . implode(", ", $defaultMethods));
                    }
                }
            ]
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }
    
        // Update the payment method name
        $method->update($validator->validated());
    
        return $this->Ok($method, "Transaction Payment Method has been updated");
    }
    
    /**
     * Remove the specified payment method.
     */
    public function destroy(string $id)
    {
        $method = TransactionPaymentMethod::find($id);

        if (empty($method)) {
            return $this->NotFound("Transaction Payment Method not found");
        }

        $method->delete();

        return $this->Ok(null, "Transaction Payment Method has been deleted");
    }

    /**
     * Store or update the transaction with a valid payment method.
     */
    public function storeTransaction(Request $request)
    {
        // Validate the payment_method_name field
        $validator = validator()->make($request->all(), [
            'payment_method_name' => 'required|string|in:Cash On Delivery,Gcash,Debit or Credit Cards,Digital Wallet',
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        // Get the payment method name from the validated input
        $paymentMethodName = $request->input('payment_method_name');

        // Find or create the payment method
        $paymentMethod = TransactionPaymentMethod::firstOrCreate([
            'name' => $paymentMethodName
        ]);

        // Create the transaction
        $transaction = new Transaction();
        $transaction->payment_method_id = $paymentMethod->id;

        // Additional transaction details (like user_id, product_id, etc.) can be added here
        // Assuming you have more fields like user_id, products, etc.
        $transaction->user_id = $request->user()->id;  // Example, assuming you are using user authentication

        $transaction->save();

        // Return success response with the created transaction
        return $this->Created($transaction, "Transaction has been created with payment method: {$paymentMethodName}");
    }
}
