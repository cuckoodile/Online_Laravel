<?php

namespace App\Http\Controllers;

use App\Models\TransactionPaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;


/**
 * @OA\Get(
 *     path="/api/transactions/payment",
 *     summary="Get all transaction payment methods",
 *     tags={"TransactionPaymentMethod"},
 *     @OA\Response(
 *         response=200,
 *         description="List of payment methods",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/TransactionPaymentMethod")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions/payment/{id}",
 *     summary="Get a specific transaction payment method by ID",
 *     tags={"TransactionPaymentMethod"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Payment method found",
 *         @OA\JsonContent(ref="#/components/schemas/TransactionPaymentMethod")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction Payment Method not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions/payment",
 *     summary="Reference an existing transaction payment method by name",
 *     tags={"TransactionPaymentMethod"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Gcash")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Transaction Payment Method has been referenced successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/transactions/payment/{id}",
 *     summary="Update a transaction payment method",
 *     tags={"TransactionPaymentMethod"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Debit or Credit Cards")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction Payment Method has been updated successfully"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction Payment Method not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/transactions/payment/{id}",
 *     summary="Delete a transaction payment method",
 *     tags={"TransactionPaymentMethod"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction Payment Method has been deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction Payment Method not found"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="TransactionPaymentMethod",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
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
        $request->validate([
            "name" => "required|string|exists:transaction_payment_methods,name"
        ]);
    
        // Retrieve the existing payment method
        $paymentMethod = TransactionPaymentMethod::where('name', $request->name)->first();
    
        return $this->Created($paymentMethod, "Transaction Payment Method has been referenced successfully");
    }


    /**
     * Update the specified transaction payment method.
     */
    public function update(Request $request, string $id)
    {
        $inputs = $request->all();
        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");

        // Find the payment method using findOrFail()
        $method = TransactionPaymentMethod::findOrFail($id);

        // Validate that the name exists in the transaction_payment_methods table
        $validator = validator()->make($inputs, [
            "name" => "required|string|exists:transaction_payment_methods,name"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        // Update the payment method
        $method->update($validator->validated());

        return $this->Ok($method, "Transaction Payment Method has been updated successfully.");
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
    // public function storeTransaction(Request $request)
    // {
    //     // Validate the payment_method_name field
    //     $validator = validator()->make($request->all(), [
    //         'payment_method_name' => 'required|string|in:Cash On Delivery,Gcash,Debit or Credit Cards,Digital Wallet',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->BadRequest($validator->errors());
    //     }

    //     // Get the payment method name from the validated input
    //     $paymentMethodName = $request->input('payment_method_name');

    //     // Find or create the payment method
    //     $paymentMethod = TransactionPaymentMethod::firstOrCreate([
    //         'name' => $paymentMethodName
    //     ]);

    //     // Create the transaction
    //     $transaction = new Transaction();
    //     $transaction->payment_method_id = $paymentMethod->id;

    //     // Additional transaction details (like user_id, product_id, etc.) can be added here
    //     // Assuming you have more fields like user_id, products, etc.
    //     $transaction->user_id = $request->user()->id;  // Example, assuming you are using user authentication

    //     $transaction->save();

    //     // Return success response with the created transaction
    //     return $this->Created($transaction, "Transaction has been created with payment method: {$paymentMethodName}");
    // }
}
