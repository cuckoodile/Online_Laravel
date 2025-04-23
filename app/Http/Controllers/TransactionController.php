<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Address;
use App\Models\Cart;
use \App\Models\Transaction;

use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['transaction_types', 'transaction_statuses', 'transaction_payment_methods', 'products', 'address'])->get();
        if ($transactions->isEmpty()) {
            return $this->NotFound("No transactions found!");
        }

        return $this->Ok($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $inputs = $request->all();
    
      
        $validator = validator()->make($inputs, [
            "payment_method_id" => "required|exists:transaction_payment_methods,id|integer",
            "type_id" => "required|exists:transaction_types,id|integer",
            "status_id" => "required|exists:transaction_statuses,id|integer",
            "region" => "required|string",
            "province" => "required|string",
            "district" => "required|string",
            "city_municipality" => "required|string",
            "barangay" => "required|string",
            "subdivision_village" => "nullable|string",
            "street" => "nullable|string",
            "lot_number" => "nullable|string|regex:/^[0-9,.'\-\s]+$/",
            "block_number" => "nullable|string|regex:/^[0-9,.'\-\s]+$/",
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
                'region' => $inputs['region'],
                'province' => $inputs['province'],
                'district' => $inputs['district'],
                'city_municipality' => $inputs['city_municipality'],
                'barangay' => $inputs['barangay'],
                'subdivision_village' => $inputs['subdivision_village'] ?? null,
                'street' => $inputs['street'] ?? null,
                'lot_number' => $inputs['lot_number'] ?? null,
                'block_number' => $inputs['block_number'] ?? null,
                'zip_code' => $inputs['zip_code']
            ]);
        }
    
       

        $transaction = $user->transactions()->create([
            'address_id' => $address->id,
            'payment_method_id' => $inputs['payment_method_id'],
            'type_id' => $inputs['type_id'],
            'status_id' => $inputs['status_id']
        ]);
    
        // Prepare pivot data for products
        $pivotData = [];
        foreach ($cartItems as $item) {
            $pivotData[$item->product_id] = [
                'quantity' => $item->quantity,
                'total_price' => $item->product->price * $item->quantity
            ];
        }
    
        
        $transaction->products()->sync($pivotData);
    
        // pang-clear lang ito after mo mag-checkout
        Cart::where('user_id', $user->id)->delete();
    
        return $this->Created(
            $transaction->load('products', 'transaction_payment_methods', 'transaction_types', 'transaction_statuses', 'address', 'cart'),
            "Transaction has been created!"
        );
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
        $transactions->address;
        $transactions->transaction_payment_methods;
        $transactions->transaction_types;
        $transactions->transaction_statuses;

        return $this->Ok($transactions);
    }

        /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $transaction = Transaction::find($id);

        if (empty($transaction)) {
            return $this->NotFound("Transaction not found!");
        }

        $inputs = $request->all();

        $validator = validator()->make($inputs, [
           "payment_method_id" => "exists:transaction_payment_methods,id|integer",
           "type_id" => "exists:transaction_types,id|integer",
           "status_id" => "exists:transaction_statuses,id|integer",
           "contact_number" => "phone:PH|required|unique:profiles|min:10|max:15",
           "region" => "string",
           "province" => "string",
           "district" => "string",
           "city_municipality" => "string",
           "barangay" => "string",
           "subdivision_village" => "string",
           "street" => "string",
           "lot_number" => "string|regex:/^[0-9,.'\-\s]+$/",
           "block_number" => "string|regex:/^[0-9,.'\-\s]+$/",
           "zip_code" => "string|min:4|regex:/^[0-9]+$/",

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
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $transaction = Transaction::find($id);

        if (empty($transaction)) {
            return $this->NotFound("Transaction not found!");
        }

        $transaction->delete();

        return $this->Ok("Transaction has been deleted!");
    }
}

