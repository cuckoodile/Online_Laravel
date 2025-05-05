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
            "cart_id" => "required|exists:carts,id|integer",
            "address_id.house_address" => "required|string",
            "address_id.region" => "required|string",
            "address_id.province" => "required|string",
            "address_id.city" => "required|string",
            "address_id.baranggay" => "required|string",
            "address_id.zip_code" => "required|string|min:4|regex:/^[0-9]+$/"
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
    
        
        $addressData = $inputs['address_id'];

        $address = Address::where('user_id', $user->id)->first();
        
        if (!$address) {
            $address = Address::create([
                'user_id' => $user->id,
                'house_address' => $addressData['house_address'],
                'region' => $addressData['region'],
                'province' => $addressData['province'],
                'city' => $addressData['city'],
                'baranggay' => $addressData['baranggay'],
                'zip_code' => $addressData['zip_code']
            ]);
        }

    // 'region' => $inputs['region'],
    // 'district' => $inputs['district'],
    // 'subdivision_village' => $inputs['subdivision_village'] ?? null,
    // 'street' => $inputs['street'] ?? null,
       


        $validator = validator()->make($inputs, [
            "user_id" => "required|exists:users,id|integer",
            "payment_method_id" => "required|exists:transaction_payment_methods,id|integer",
            "type_id" => "required|exists:transaction_types,id|integer",
            "status_id" => "required|exists:transaction_statuses,id|integer",
            "cart_id" => "required|exists:carts,id|integer",
        
            // Validation for the nested address object
            "address_id.house_address" => "required|string",
            "address_id.region" => "required|string",
            "address_id.province" => "required|string",
            "address_id.city" => "required|string",
            "address_id.baranggay" => "required|string",
            "address_id.zip_code" => "required|string|min:4|regex:/^[0-9]+$/"
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
            $addressData = $inputs['address_id']; // Extract nested address data
        
            $address = Address::create([
                'user_id' => $user->id,
                'house_address' => $addressData['house_address'],
                'region' => $addressData['region'],
                'province' => $addressData['province'],
                'city' => $addressData['city'],
                'baranggay' => $addressData['baranggay'],
                'zip_code' => $addressData['zip_code']
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
            "cart_id" => "exists:carts,id|integer",
            "user_id" => "exists:users,id|integer",

            // Nested address validation
            "address_id.house_address" => "sometimes|string",
            "address_id.region" => "sometimes|string",
            "address_id.province" => "sometimes|string",
            "address_id.city" => "sometimes|string",
            "address_id.baranggay" => "sometimes|string",
            "address_id.zip_code" => "sometimes|string|min:4|regex:/^[0-9]+$/",
    
            // Product validation (pivot table)
            "products" => "array",
            "products.*" => "array",
            "products.*.product_id" => "exists:products,id|integer",
            "products.*.quantity" => "integer|min:1"
    
        ], [
            "contact_number.phone" => "The :attribute must be a valid phone number"
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }
    
        // Update transaction fields
        $transaction->update($validator->validated());
    
        if (isset($inputs['address_id'])) {
            $address = Address::find($inputs['address_id']);
        
            if ($address && isset($inputs['address'])) {
                $address->update(array_filter([
                    'house_address' => $inputs['address']['house_address'] ?? $address->house_address,
                    'region' => $inputs['address']['region'] ?? $address->region,
                    'province' => $inputs['address']['province'] ?? $address->province,
                    'city' => $inputs['address']['city'] ?? $address->city,
                    'baranggay' => $inputs['address']['baranggay'] ?? $address->baranggay,
                    'zip_code' => $inputs['address']['zip_code'] ?? $address->zip_code
                ]));
            }
        
            $transaction->update(['address_id' => $address->id]);  
        }
    
        // Update products if provided
        if (isset($inputs['products'])) {
            $items = [];
            foreach ($inputs['products'] as $product) {
                $productModel = Product::find($product['product_id']);
    
                if ($productModel) {
                    $items[$product['product_id']] = [
                        "total_price" => $productModel->price * $product['quantity'],
                        "quantity" => $product['quantity']
                    ];
                }
            }
    
            $transaction->products()->sync($items);
        }
    
        return $this->Ok($transaction->load('products', 'address'), "Transaction has been updated!");
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
