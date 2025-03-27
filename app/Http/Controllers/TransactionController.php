<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use \App\Models\Transaction;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with('transaction_types')->with('transaction_statuses')->with('transaction_payment_methods')->get();

        return $this->Ok($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $inputs = $request->all();

        $validator = validator()->make($inputs,[
        //    "user_id" => "required|exists:users,id|integer" ,
           "payment_method_id" => "required|exists:transaction_payment_methods,id|integer" ,
           "type_id" => "required|exists:transaction_types,id|integer",
           "status_id" => "required|exists:transaction_statuses,id|integer",

           "products" => "required|array",
           "products.*" => "array",
           "products.*.product_id" => "required|exists:products,id|integer",
           "products.*.quantity" => "required|integer|min:1"
        ]);

        if($validator->fails()){
            return $this->BadRequest($validator);
        }

        Log::info($request->user());

        $transactions =  $request->user()->transaction()->create($validator->validated());

        $items = [];
        $products = Product::all();


        foreach($inputs['products'] as $product){
            $items[$product['product_id']] = [
                "price" => $products->where('id',$product['product_id'])->first()->price,
                "quantity" => $product['quantity']
            ];
        }

        $transactions->products()->sync($items);

        $transactions->products;

        return $this->Created($transactions, "Transaction has been created!");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transactions = Transaction::find($id);

        if(empty($transactions)){
            return $this->NotFound("Transaction not found!");
        }

        $transactions->products;
        $transactions->transaction_payment_method;
        $transactions->transaction_type;
        $transactions->transaction_status;

        return $this->Ok($transactions);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
