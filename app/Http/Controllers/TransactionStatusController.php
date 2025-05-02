<?php

namespace App\Http\Controllers;

use App\Models\TransactionStatus;
use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionStatusController extends Controller
{
    /**
     * Display a listing of all transaction statuses.
     */
    public function index() 
    {
        $statuses = TransactionStatus::all();
        
        return $this->Ok($statuses);
    }

    /**
     * Display the specified transaction status.
     */
    public function show(string $id)
    {
        $status = TransactionStatus::find($id);
        
        if (empty($status)) {
            return $this->NotFound("Transaction Status not found");
        }

        $status->transactions;

        return $this->Ok($status);
    }

    /**
     * Store a newly created transaction status.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|exists:transaction_statuses,name"
        ]);
    
        // Retrieve the existing status instead of creating a new one
        $status = TransactionStatus::where('name', $request->name)->first();
    
        return $this->Created($status, "Transaction Status has been referenced successfully");
    }     

    /**
     * Update the specified transaction status.
     */
    public function update(Request $request, string $id)
    {
        $inputs = $request->all();
        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");

        // Find the existing transaction status
        $status = TransactionStatus::find($id);

        // Validate the name exists in transaction_statuses
        $validator = validator()->make($inputs, [
            "name" => "required|string|exists:transaction_statuses,name"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        // Update the transaction status
        $status->update($validator->validated());

        return $this->Ok($status, "Transaction Status has been updated successfully.");
    }
    
    /**
     * Remove the specified transaction status.
     */
    public function destroy(string $id)
    {
        $status = TransactionStatus::find($id);

        if (empty($status)) {
            return $this->NotFound("Transaction Status not found");
        }

        $status->delete();

        return $this->Ok(null, "Transaction Status has been deleted");
    }

    /**
     * Store or update the transaction with a valid status.
     */
    // public function storeTransaction(Request $request)
    // {
    //     // Validate the transaction_status_name field
    //     $validator = validator()->make($request->all(), [
    //         'transaction_status_name' => 'required|string|in:pending,confirmed,shipped,delivered,cancelled,returned,refunded',
    //     ]);

    //     if ($validator->fails()) {
    //         return $this->BadRequest($validator->errors());
    //     }

    //     // Get the status name from the validated input
    //     $statusName = $request->input('transaction_status_name');

    //     // Find or create the transaction status
    //     $status = TransactionStatus::firstOrCreate([
    //         'name' => $statusName
    //     ]);

    //     // Create the transaction
    //     $transaction = new Transaction();
    //     $transaction->transaction_status_id = $status->id;

    //     // Additional transaction details (like user_id, payment_method_id, etc.) can be added here
    //     // Assuming you have more fields like user_id, products, etc.
    //     $transaction->user_id = $request->user()->id;  // Example, assuming you are using user authentication

    //     $transaction->save();

    //     // Return success response with the created transaction
    //     return $this->Created($transaction, "Transaction has been created with status: {$statusName}");
    // }
}
