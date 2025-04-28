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
<<<<<<< HEAD
=======
    
      
        $validator = validator()->make($inputs, [
            "payment_method_id" => "required|exists:transaction_payment_methods,id|integer",
            "type_id" => "required|exists:transaction_types,id|integer",
            "status_id" => "required|exists:transaction_statuses,id|integer",
            "house_address"=>"required|string",
            "region"=>"required|string",
            "province" => "required|string",
            "city" => "required|string",
            "baranggay" => "required|string",
            "zip_code" => "required|string|min:4|regex:/^[0-9]+$/"
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }
    
        $user = $request->user();
    
        // Get all items from the user's cart
        $cartItems = Cart::with('product')->where('user_id', $user->id)->get();
        if ($cartItems->isEmpty()) {
            return $this->BadRequest("Your cart is empty!");
        }
    
        
        $address = Address::where('user_id', $user->id)->first();
    
        // If the user doesn't have an address, create one
        if (!$address) {
            $address = Address::create([
                'user_id' => $user->id,
                'house_address' => $inputs['house_address'],
                'region' => $inputs['region'],
                'province' => $inputs['province'],
                'city' => $inputs['city'],
                'baranggay' => $inputs['baranggay'],
                'zip_code' => $inputs['zip_code']

            ]);
        }

    // 'region' => $inputs['region'],
    // 'district' => $inputs['district'],
    // 'subdivision_village' => $inputs['subdivision_village'] ?? null,
    // 'street' => $inputs['street'] ?? null,
       
>>>>>>> 69bff22 (Product Comments)

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
<<<<<<< HEAD
        //
=======
        $transaction = Transaction::find($id);

        if (empty($transaction)) {
            return $this->NotFound("Transaction not found!");
        }

        $inputs = $request->all();

        $validator = validator()->make($inputs, [
           "payment_method_id" => "exists:transaction_payment_methods,id|integer",
           "type_id" => "exists:transaction_types,id|integer",
           "status_id" => "exists:transaction_statuses,id|integer",
           "contact_number" => "phone:PH|sometimes|unique:profiles|min:11|max:11",
           "house_address"=>"sometimes|string",
           "region"=>"sometimes|string",
           "province" => "sometimes|string",
           "city" => "sometimes|string",
           "baranggay" => "sometimes|string",
           "zip_code" => "sometimes|string|min:4|regex:/^[0-9]+$/",

            // PIVOT TABLE: This part is only for product validation kaya di ko na nilagay sa store function
           "products" => "array",
           "products.*" => "array",
           "products.*.product_id" => "exists:products,id|integer",
           "products.*.quantity" => "integer|min:1"

        ], [
            "contact_number.phone" => "The :attribute must be a valid phone number",
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $transaction->update($validator->validated());

        if (isset($inputs['products'])) {
            $items = [];
            $products = Product::all();

            foreach ($inputs['products'] as $product) {
                $items[$product['product_id']] = [
                    "total_price" => $products->where('id', $product['product_id'])->first()->total_price,
                    "quantity" => $product['quantity']
                ];
            }

            $transaction->products()->sync($items);
        }

        $transaction->products;

        return $this->Ok($transaction, "Transaction has been updated!");
>>>>>>> 69bff22 (Product Comments)
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
