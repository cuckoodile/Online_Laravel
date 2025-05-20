<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Address;
use App\Models\Cart;
use \App\Models\Transaction;

use Illuminate\Http\Request;


/**
 * @OA\Get(
 *     path="/api/transactions",
 *     summary="Get all transactions",
 *     tags={"Transaction"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of transactions",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Transaction")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No transactions found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/transactions",
 *     summary="Create a new transaction",
 *     tags={"Transaction"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"payment_method_id","type_id","status_id","address_id","products"},
 *             @OA\Property(property="payment_method_id", type="integer", example=1),
 *             @OA\Property(property="type_id", type="integer", example=1),
 *             @OA\Property(property="status_id", type="integer", example=1),
 *             @OA\Property(property="address_id", type="integer", example=1),
 *             @OA\Property(
 *                 property="products",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="product_id", type="integer", example=1),
 *                     @OA\Property(property="quantity", type="integer", example=2)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Transaction created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error or insufficient stock"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/transactions/{id}",
 *     summary="Get a specific transaction by ID",
 *     tags={"Transaction"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction found",
 *         @OA\JsonContent(ref="#/components/schemas/Transaction")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/transactions/{id}",
 *     summary="Update a transaction",
 *     tags={"Transaction"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="payment_method_id", type="integer"),
 *             @OA\Property(property="type_id", type="integer"),
 *             @OA\Property(property="status_id", type="integer"),
 *             @OA\Property(property="cart_id", type="integer"),
 *             @OA\Property(property="user_id", type="integer"),
 *             @OA\Property(property="address_id", type="integer"),
 *             @OA\Property(
 *                 property="products",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="product_id", type="integer"),
 *                     @OA\Property(property="quantity", type="integer")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/transactions/{id}",
 *     summary="Delete a transaction",
 *     tags={"Transaction"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transaction deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transaction not found"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Transaction",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="payment_method_id", type="integer"),
 *     @OA\Property(property="type_id", type="integer"),
 *     @OA\Property(property="status_id", type="integer"),
 *     @OA\Property(property="address_id", type="integer"),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Property(property="total", type="number", format="float"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $transactions = Transaction::with(['products', 'transaction_types', 'transaction_statuses', 'transaction_payment_methods', 'address'])->get();

        if ($transactions->isEmpty()) {
            return $this->NotFound("No transactions found!");
        }

        $transactions->each(function ($transaction) {
            $transaction->total = $transaction->products->sum('pivot.sub_total');
        });

        return $this->Ok($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {
        $inputs = $request->all();

        $validator = validator()->make($inputs,[
           "payment_method_id" => "required|exists:transaction_payment_methods,id|integer" ,
           "type_id" => "required|exists:transaction_types,id|integer",
           "status_id" => "required|exists:transaction_statuses,id|integer",

           "address_id" => "required|exists:addresses,id|integer",

           "products" => "required|array",
           "products.*" => "array",
           "products.*.product_id" => "required|exists:products,id|integer",
           "products.*.quantity" => "required|integer|min:1",
        ]);

        if($validator->fails()){
            return $this->BadRequest($validator);
        }

        $products = Product::whereIn('id', collect($inputs['products'])->pluck('product_id'))->get();

        foreach ($inputs['products'] as $product) {
            $productModel = $products->where('id', $product['product_id'])->first();

            if ($inputs['type_id'] == 2) {
                $inboundStock = $productModel->transactions()
                    ->where('type_id', 1)
                    ->sum('product_transaction.quantity');

                $outboundStock = $productModel->transactions()
                    ->where('type_id', 2)
                    ->sum('product_transaction.quantity');

                $availableStock = $inboundStock - $outboundStock;

                if (!$productModel || $product['quantity'] > $availableStock) {
                    return $this->BadRequest("The quantity for product ID {$product['product_id']} exceeds the available stock. Available: {$availableStock}, Attempt: {$product['quantity']}");
                }
            }
        }

        $transactions =  $request->user()->transactions()->create($validator->validated());

        $items = [];
        foreach ($inputs['products'] as $product) {
            $productModel = $products->where('id', $product['product_id'])->first();
            $product_quantity = $product['quantity'];

            $items[$product['product_id']] = [
                "price" => $productModel->price,
                "quantity" => $product_quantity,
                "sub_total" => $product_quantity * $productModel->price,
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
