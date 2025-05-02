<?php

namespace App\Http\Controllers;

use App\Models\TransactionType;
use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    /**
     * Display a listing of all transaction types.
    */
    public function index() 
    {
        $types = TransactionType::all();
        
        return $this->Ok($types);
    } 

     /**
     * Display the specified transaction types.
    */
    public function show(string $id)
    {
        $types = TransactionType::find($id);
        
        if (empty($types)) {
            return $this->NotFound("Transaction Type not found");
        }

        $types->transactions;

        return $this->Ok($types);
    }


    /**
     * Store a newly created transaction types.
     */
    public function store(Request $request)
    {
        $request->validate([
            "name" => "required|string|exists:transaction_types,name"
        ]);
    
        // Retrieve the existing status instead of creating a new one
        $status = TransactionType::where('name', $request->name)->first();
    
        return $this->Created($status, "Transaction Type has been referenced successfully");
    }   

    /**
     * Update the specified transaction type.
     */
    public function update(Request $request, string $id)
    {
        $inputs = $request->all();
        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");

       
        $type = TransactionType::find($id);

        // Validate the name exists in the transaction_types table
        $validator = validator()->make($inputs, [
            "name" => "required|string|exists:transaction_types,name"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        // Update the transaction type
        $type->update($validator->validated());

        return $this->Ok($type, "Transaction Type has been updated successfully.");
    }

    

    public function destroy(string $id)
    {
        $types = TransactionType::find($id);

        if (empty($types)) {
            return $this->NotFound("Transaction Type not found");
        }

        $types->delete();

        return $this->Ok(null, "Transaction Type has been deleted");
    }
}
