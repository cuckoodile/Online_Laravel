<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            $product->stock = $product->stock; // Include stock as a temporary attribute
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

        return $this->Ok($Product);
    }

    public function getProductImage(string $id)
    {
        $Product = Product::find($id);

        if (!$Product) {
            return $this->NotFound("Product Image not found");
        }

        $imageUrls = collect(json_decode($Product->product_image))->map(function ($image) {
            return asset("images/{$image}");
        });

        return $this->Ok($imageUrls);
    }

    public function store(Request $request)
    {
        $inputs = $request->all();
    
        // Sanitize name
        $inputs["name"] = $this->SanitizedName($inputs["name"]);
    
        $validator = validator()->make($inputs, [
            "name" => "required|string|unique:products",
            "product_image" => "required|array",
            "product_image.*" => "image|mimes:jpeg,png,jpg,gif|max:2048",
            "admin_id" => "sometimes|exists:users,id|integer",
            "price" => "required|numeric",
            "stock" => "required|integer|min:0|max:10000",
            "description" => "required|string",
            "category_id" => "required|exists:categories,id|integer"
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "errors" => $validator->errors(),
                "message" => "Validation Failed!"
            ], 400);
        }
    
        $imageNames = [];
    
        if ($request->hasFile('product_image')) {
            foreach ($request->file('product_image') as $image) {
                $singleImageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('images'), $singleImageName);
                $imageNames[] = $singleImageName;
            }
        }
    
        $Product = Product::create(array_merge(
            $validator->validated(),
            ["product_image" => json_encode($imageNames)]
        ));
    
        return $this->Created($Product, "Product has been created");
    }


    public function update(Request $request, string $id)
    {
        $inputs = $request->all();
    
        // Sanitize the name if provided
        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");
        if (empty($inputs["name"])) {
            unset($inputs["name"]);
        }
    
        // Validation rules
        $validator = validator()->make($inputs, [
            "name" => "sometimes|string|unique:products,name," . $id,
            "product_image" => "sometimes|array",
            "product_image.*" => "image|mimes:jpeg,png,jpg,gif|max:2048",
            "admin_id" => "sometimes|exists:users,id|integer",
            "price" => "sometimes|numeric",
            "stock" => "sometimes|integer|min:0|max:10000",
            "description" => "sometimes|string",
            "category_id" => "sometimes|exists:categories,id|integer"
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                "ok" => false,
                "errors" => $validator->errors(),
                "message" => "Validation Failed!"
            ], 400);
        }
    
        // Find the product
        $Product = Product::find($id);
        if (empty($Product)) {
            return $this->NotFound("Product not found");
        }
    
        // Handle product_image files if provided
        if ($request->hasFile('product_image')) {
            $imageNames = $Product->product_image ? json_decode($Product->product_image, true) : [];
    
            foreach ($request->file('product_image') as $uploadedImage) {
                $singleImageName = time() . '_' . $uploadedImage->getClientOriginalName();
                $uploadedImage->move(public_path('images'), $singleImageName);
                $imageNames[] = $singleImageName;
            }
    
            $inputs['product_image'] = json_encode($imageNames);
        } else {
            // If no new images are being uploaded but product_image is in request, remove it
            unset($inputs['product_image']);
        }
    
        // Update the product
        $Product->update($inputs);
    
        return $this->Ok($Product, "Product has been updated");
    }
    
    
    

    public function destroy(string $id)
    {
        $Product = Product::find($id);

        if (empty($Product)) {
            return $this->NotFound("Product not found");
        }

        $Product->delete();

        return $this->Ok(null, "Product has been deleted");
    }
}
