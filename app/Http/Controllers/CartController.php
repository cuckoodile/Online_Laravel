<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
        public function index(Request $request)
    {
        $userId = $request->user()->id; 
        $carts = Cart::with('product')
                    ->where('user_id', $userId)
                    ->get();

        if ($carts->isEmpty()) {
            return $this->NotFound("No items in the cart");
        }

        return $this->Ok($carts);
    }

    public function show(Request $request, string $id)
    {
        
        $userId = $request->user()->id; 
        $cart = Cart::with('product')
                    ->where('user_id', $userId)
                    ->where('id', $id)
                    ->first();

        if (empty($cart)) {
            return $this->NotFound("Cart item not found");
        }

        return $this->Ok($cart);
    }

    public function store(Request $request)
    {
        $validator = validator()->make($request->all(), [
            "user_id" => "required|exists:users,id",
            "product_id" => "required|exists:products,id",
            "quantity" => "required|integer|min:1|max:255"
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }
    
        $validated = $validator->validated();
    
        // Get the product price
        $product = Product::find($validated['product_id']);
        if (!$product) {
            return $this->NotFound("Product not found");
        }
    
        $cart = Cart::where('user_id', $validated['user_id'])
                    ->where('product_id', $validated['product_id'])
                    ->first();
    
        if ($cart) {
            // Update quantity and total price
            $cart->quantity += $validated['quantity'];
            $cart->total_price = $cart->quantity * $product->price;
            $cart->save();
        } else {
            // Create new cart entry with calculated total price
            $cart = Cart::create([
                'user_id' => $validated['user_id'],
                'product_id' => $validated['product_id'],
                'quantity' => $validated['quantity'],
                'total_price' => $validated['quantity'] * $product->price,
            ]);
        }
    
        return $this->Created($cart, "Product added to cart!");
    }
    


    public function update(Request $request, string $id)
    {
        $validator = validator()->make($request->all(), [
            "quantity" => "required|integer|min:1",
        ]);
    
        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }
    
        $cart = Cart::find($id);
    
        if (empty($cart)) {
            return $this->NotFound("Cart item not found");
        }
    
        // Get the product price
        $product = $cart->product;
        if (!$product) {
            return $this->NotFound("Associated product not found");
        }
    
        // Update quantity and total price
        $cart->quantity = $request->quantity;
        $cart->total_price = $request->quantity * $product->price;
        $cart->save();
    
        return $this->Ok($cart, "Cart item has been updated");
    }
    

    public function destroy(Request $request, string $id)
    {
        
        $userId = $request->user()->id;
        $cart = Cart::where('id', $id)
                    ->where('user_id', $userId)
                    ->first();

        if (empty($cart)) {
            return $this->NotFound("Cart item not found or does not belong to the user");
        }

        $cart->delete();

        return $this->Ok(null, "Cart item has been deleted");
    }
}
