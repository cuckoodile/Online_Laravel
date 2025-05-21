<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;


/**
 * @OA\Get(
 *     path="/api/carts",
 *     summary="Get all cart items for the authenticated user",
 *     tags={"Cart"},
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="List of cart items",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Cart")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No items in the cart"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/carts/{id}",
 *     summary="Get a specific cart item by ID for the authenticated user",
 *     tags={"Cart"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart item found",
 *         @OA\JsonContent(ref="#/components/schemas/Cart")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Cart item not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/carts",
 *     summary="Add a product to the cart",
 *     tags={"Cart"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"user_id","product_id","quantity"},
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="quantity", type="integer", example=2)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product added to cart"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/carts/{id}",
 *     summary="Update the quantity of a cart item",
 *     tags={"Cart"},
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
 *             required={"quantity"},
 *             @OA\Property(property="quantity", type="integer", example=3)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart item has been updated"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Cart item not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/carts/{id}",
 *     summary="Delete a cart item",
 *     tags={"Cart"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cart item has been deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Cart item not found or does not belong to the user"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Cart",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="quantity", type="integer"),
 *     @OA\Property(property="total_price", type="number", format="float"),
 *     @OA\Property(property="product", ref="#/components/schemas/Product")
 * )
 */
class CartController extends Controller
{
        public function index(Request $request)
    {
        $userId = $request->user()->id; 

        $carts = Cart::with('product')
                    ->where('user_id', $userId)
                    ->get();

        foreach ($carts as $cart) {
            $product = $cart->product;
            if ($product) {
                $images = $product->product_image;
                if (is_string($images)) {
                    $images = json_decode($images, true);
                }
                $product->product_image = array_map(function ($img) {
                    // return "http://127.0.0.1:8000/{$img}";
                    return "https://devsixtech.styxhydra.com/{$img}";
                }, $images ?? []);
            }
        }

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

        $product = $cart->product;
        if ($product) {
            $images = $product->product_image;
            if (is_string($images)) {
                $images = json_decode($images, true);
            }
            $product->product_image = array_map(function ($img) {
                // return "http://127.0.0.1:8000/{$img}";
                return "https://devsixtech.styxhydra.com/{$img}";
            }, $images ?? []);
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
