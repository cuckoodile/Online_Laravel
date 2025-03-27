<?php

namespace App\Http\Controllers;

use App\Models\TransactionPaymentMethod;
use Illuminate\Http\Request;

class TransactionPaymentMethodCrontroller extends Controller
{

    public function index() 
    {
        $methods = TransactionPaymentMethod::all();
        
        return $this->Ok($methods);
    } 

    public function show(string $id)
    {
        $methods = TransactionPaymentMethod::find($id);
        
        if (empty($methods)) {
            return $this->NotFound("Transaction Status not found");
        }

        $methods->transactions;

        return $this->Ok($methods);
    }

    public function store (Request $request)
    {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"]);

        $validator = validator()->make($request->all(), [
            "name" => "required|string|unique:transaction_payment_methods,name"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $methods = TransactionPaymentMethod::create($validator->validated());

        return $this->Created($methods, "Transaction Status has been created");
    }


    public function update (Request $request, string $id) {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");

        $validator = validator()->make($request->all(), [
            "name" => "required|string|unique:transaction_payment_methods,name,$id|"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        $methods = TransactionPaymentMethod::find($id);

        if (empty($methods)) {
            return $this->NotFound("transaction_paymentmethods not found");
        }

        $methods->update($validator->validated());

        return $this->Ok($methods, "transaction_paymentmethods has been updated");
        
    }

    public function destroy(string $id)
    {
        $methods = TransactionPaymentMethod::find($id);

        if (empty($methods)) {
            return $this->NotFound("transaction_paymentmethods not found");
        }

        $methods->delete();

        return $this->Ok(null, "transaction_paymentmethods has been deleted");
    }
}
