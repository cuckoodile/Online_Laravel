<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();

        foreach ($products as $product) {
            $product->product_specifications;
            $product->product_image;
            $product->category;
            $product->product_comments;
            $product->stock = $product->stock; 
        }
        
        return $this->Ok($products);
    } 

    public function show(string $id)
    {
        $Product = Product::find($id);
        
        if (empty($Product)) {
            return $this->NotFound("Product not found");
        }

        $Product->product_specifications;
        $Product->product_image;
        $Product->category;
        $Product->product_comments;
        $Product->stock = $Product->stock;

        return $this->Ok($Product);
    }

    public function getProductImage(string $id)
    {
        $Product = Product::find($id); // if you put findOrFail it will automatically return 404 error if product not found

        if (!$Product) {
            return $this->NotFound("Product Image not found"); // so this may not need to be used
        }

        $imageUrls = collect(json_decode($Product->product_image))->map(function ($image) {
            return asset("images/{$image}");
        });

        return $this->Ok($imageUrls);
    }


    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            "name" => "required|string|unique:products",
            "product_image" => "required|array|min:1|max:10",
            "product_image.*" => "required|image|mimes:jpeg,png,jpg,gif,webp,jfif", // we can add additional extensions if needed
            "price" => "required|numeric",
            "description" => "required|string",
            "category_id" => "required|exists:categories,id",
            "product_specifications" => "required|array|min:2|max:10",
            "product_specifications.*.details" => "required|array"
        ]);

        // Sanitize product name
        $name = $this->SanitizedName($request->name);

        // Upload images and collect paths
        $imagePaths = [];
        foreach ($request->file('product_image') as $file) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $file->move(public_path('product_images'), $fileName);
            $imagePaths[] = 'product_images/' . $fileName;
        }

        // Create product record
        $product = Product::create([
            'name' => $name,
            'price' => $request->price,
            'admin_id' => $request->user()->id,
            'product_image' => $imagePaths,
            'description' => $request->description,
            'category_id' => $request->category_id,
        ]);

        // Save specifications
            foreach ($request->product_specifications as $spec) {
                $product->product_specifications()->create([
                    'details' => json_encode($spec['details']),
                ]);
            }

            // If stock is provided, create an inbound transaction
            $transaction = Transaction::create([
                'user_id' => $request->user()->id,
                'type_id' => 1, // Inbound transaction type
                'status_id' => 1, // Default status (e.g., Pending)
                'payment_method_id' => 1, // Default payment method (e.g., Cash)
                'address_id' => 1, // Default address (can be adjusted as needed)
            ]);

            $transaction->products()->attach($product->id, [
                'quantity' => $request->stock,
                'price' => $product->price,
                'sub_total' => $request->stock * $product->price,
            ]);

            $product->stock = $product->stock; // Include dynamically calculated stock in the response

            return $this->Created($product, "Product has been created with specifications and stock!");
    }
    


    public function update(Request $request, Product $product)
{

    Log::info('Starting update for product: '.$product->id);
    Log::info('Request data:', $request->all());
    // Define validation rules for all possible fields
    $validator = validator()->make($request->all(), [
        "name" => "sometimes|string|unique:products,name,".$product->id,
        "product_image" => "sometimes|array|min:1|max:10",
        "product_image.*" => "sometimes|image|mimes:jpeg,png,jpg,gif,webp,jfif",
        "price" => "sometimes|numeric",
        "description" => "sometimes|string",
        "category_id" => "sometimes|exists:categories,id",
        "product_specifications" => "sometimes|array|min:2|max:10",
        "product_specifications.*.details" => "sometimes|array",
        "stock" => "sometimes|numeric|min:0"
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    // Update name if provided
    if ($request->has('name')) {
        $product->name = $this->SanitizedName($request->name);
    }

    // Update images if provided
    if ($request->hasFile('product_image')) {
        $imagePaths = [];
        foreach ($request->file('product_image') as $file) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time().'_'.uniqid().'.'.$extension;
            $file->move(public_path('product_images'), $fileName);
            $imagePaths[] = 'product_images/'.$fileName;
        }
        
        // Delete old images if they exist
        if (is_array($product->product_image)) {
            foreach ($product->product_image as $oldImage) {
                if (file_exists(public_path($oldImage))) {
                    unlink(public_path($oldImage));
                }
            }
        }
        
        $product->product_image = $imagePaths;
    }

    // Update other simple fields if provided
    $simpleFields = ['price', 'description', 'category_id'];
    foreach ($simpleFields as $field) {
        if ($request->has($field)) {
            $product->$field = $request->$field;
        }
    }

    // Save only if there are changes
    if ($product->isDirty()) {
        $product->save();
    }

    // Update specifications if provided
    if ($request->has('product_specifications')) {
        $product->product_specifications()->delete();
        foreach ($request->product_specifications as $spec) {
            $product->product_specifications()->create([
                'details' => json_encode($spec['details']),
            ]);
        }
    }

    // Handle stock adjustment if provided
    if ($request->has('stock') && $request->stock != 0) {
        $transactionType = $request->stock > 0 ? 1 : 2;
        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'type_id' => $transactionType,
            'status_id' => 1,
            'payment_method_id' => 1,
            'address_id' => 1,
        ]);

        $transaction->products()->attach($product->id, [
            'quantity' => abs($request->stock),
            'price' => $product->price,
            'sub_total' => abs($request->stock) * $product->price,
        ]);
    }

    // Return the updated product
    return $this->Ok($product, "Product has been updated successfully!");
}


public function destroy($id)
{
    // Find the product by ID
    $product = Product::findOrFail($id);

    // Optionally, delete related transactions
    $product->transactions()->detach(); // Detach the product from any transactions (if needed)
    
    // Delete product specifications
    $product->product_specifications()->delete();

    // Delete the product images from storage (if you want to delete the actual images)
    foreach ($product->product_image as $imagePath) {
        $imagePath = public_path($imagePath);
        if (file_exists($imagePath)) {
            unlink($imagePath); // Delete the image file from the server
        }
    }

    // Delete the product
    $product->delete();

    return $this->Ok("Product has been successfully deleted.");
}

}
