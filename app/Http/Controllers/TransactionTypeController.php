<?php

namespace App\Http\Controllers;

use App\Models\transaction_type;
use App\Models\TransactionType;
use Illuminate\Http\Request;

class TransactionTypeController extends Controller
{
    public function index() 
    {
        $types = TransactionType::all();
        
        return $this->Ok($types);
    } 

    public function show(string $id)
    {
        $types = TransactionType::find($id);
        
        if (empty($types)) {
            return $this->NotFound("Transaction Type not found");
        }

        $types->transactions;

        return $this->Ok($types);
    }

    public function store (Request $request)
    {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"]);

        $validator = validator()->make($request->all(), [
            "name" => "required|string|unique:transaction_types,name"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $types = TransactionType::create($validator->validated());

        return $this->Created($types, "Transaction Type has been created");
    }


    public function update (Request $request, string $id) {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");

        $validator = validator()->make($request->all(), [
            "name" => "required|string|unique:transaction_types,name,$id|"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        $types = TransactionType::find($id);

        if (empty($types)) {
            return $this->NotFound("Transaction_type not found");
        }

        $types->update($validator->validated());

        return $this->Ok($types, "Transaction_type has been updated");
        
    }

    public function destroy(string $id)
    {
        $types = TransactionType::find($id);

        if (empty($types)) {
            return $this->NotFound("Transaction_type not found");
        }

        $types->delete();

        return $this->Ok(null, "Transaction_type has been deleted");
    }
}
