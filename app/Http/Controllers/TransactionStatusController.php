<?php

namespace App\Http\Controllers;

use App\Models\transaction_status;
use App\Models\TransactionStatus;
use Illuminate\Http\Request;

class TransactionStatusController extends Controller
{
    public function index() 
    {
        $status = TransactionStatus::all();
        
        return $this->Ok($status);
    } 

    public function show(string $id)
    {
        $status = TransactionStatus::find($id);
        
        if (empty($status)) {
            return $this->NotFound("Transaction Status not found");
        }

        $status->transactions;

        return $this->Ok($status);
    }

    public function store (Request $request)
    {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"]);

        $validator = validator()->make($request->all(), [
            "name" => "required|string|unique:transaction_statuses,name"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $status = TransactionStatus::create($validator->validated());

        return $this->Created($status, "Transaction Status has been created");
    }


    public function update (Request $request, string $id) {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");

        $validator = validator()->make($request->all(), [
            "name" => "required|string|unique:transaction_statuses,name,$id|"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        $status = TransactionStatus::find($id);

        if (empty($status)) {
            return $this->NotFound("Transaction_statuses not found");
        }

        $status->update($validator->validated());

        return $this->Ok($status, "Transaction_statuses has been updated");
        
    }

    public function destroy(string $id)
    {
        $status = TransactionStatus::find($id);

        if (empty($status)) {
            return $this->NotFound("Transaction_statuses not found");
        }

        $status->delete();

        return $this->Ok(null, "Transaction_statuses has been deleted");
    }
}
