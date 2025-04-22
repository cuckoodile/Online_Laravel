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
        // Define default transaction types first
        $defaultTypes = ['Inbound', 'Outbound', 'Void', 'Returned', 'Refunded'];
    
        $inputs = $request->all();
        $inputs["name"] = $this->SanitizedName($inputs["name"]);
    
        // Apply validation with a custom message
        $validator = validator()->make($inputs, [
            "name" => [
                "required",
                "string",
                function ($attribute, $value, $fail) use ($defaultTypes) {
                    if (!in_array($value, $defaultTypes)) {
                        $fail("Invalid Type! Please choose only the available types: " . implode(", ", $defaultTypes));
                    }
                }
            ]
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }
    
        // Ensure default transaction types exist in the database
        foreach ($defaultTypes as $type) {
            if (!TransactionType::where('name', $type)->exists()) {
                TransactionType::create(['name' => $type]);
            }
        }
    
        $type = TransactionType::create($validator->validated());
    
        return $this->Created($type, "Transaction Type has been created");
    }    

    /**
     * Store the specified transaction types.
     */
    public function update(Request $request, string $id) 
    {
        // Define default transaction types first
        $defaultTypes = ['Inbound', 'Outbound', 'Void', 'Returned', 'Refunded'];
    
        $inputs = $request->all();
        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");
    
        // Find the transaction type before validation
        $types = TransactionType::find($id);
    
        if (empty($types)) {
            return $this->NotFound("Transaction Type not found");
        }
    
        // Apply validation with a custom message
        $validator = validator()->make($inputs, [
            "name" => [
                "required",
                "string",
                function ($attribute, $value, $fail) use ($defaultTypes) {
                    if (!in_array($value, $defaultTypes)) {
                        $fail("Invalid Type! Please choose only the available types: " . implode(", ", $defaultTypes));
                    }
                }
            ]
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }
    
        // Update the transaction type
        $types->update($validator->validated());
    
        return $this->Ok($types, "Transaction Type has been updated");
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
