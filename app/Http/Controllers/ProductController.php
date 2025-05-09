<?php

namespace App\Http\Controllers;

use App\Models\Product;
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
    
        // Sanitize product name to prevent XSS
        $inputs["name"] = $this->SanitizedName($inputs["name"]);
    
        /* 
         * Image Processing Pipeline:
         * 1. Convert Base64 strings to files if present
         * 2. Keep existing file upload handling
         */
        $processedImages = [];
        
        if (isset($inputs['product_image']) && is_array($inputs['product_image'])) {
            foreach ($inputs['product_image'] as $key => $image) {
                if (is_string($image) && preg_match('/^data:image\/(\w+);base64,/', $image)) {
                    // Process Base64 image
                    $filename = $this->processProductImage($image);
                    if (!$filename) {
                        return response()->json([
                            "ok" => false,
                            "errors" => ["product_image.$key" => ["Invalid Base64 image format"]],
                            "message" => "Validation Failed!"
                        ], 400);
                    }
                    $processedImages[] = $filename;
                } elseif ($request->hasFile("product_image.$key")) {
                    // Handle traditionally uploaded files
                    $processedImages[] = $image; // Will be processed in validation
                }
            }
        }
    
        // Validation rules (supports both file objects and processed filenames)
        $validator = validator()->make($inputs, [
            "name" => "required|string|unique:products",
            "product_image" => "required|array|min:1",
            "product_image.*" => [
                function ($attribute, $value, $fail) {
                    if (
                        !($value instanceof UploadedFile) &&
                        !is_string($value)
                    ) {
                        $fail('Each product image must be a file or valid Base64 string.');
                    }
                },
                'max:2048' // Applies to both files and Base64 decoded size
            ],
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
    
        $imageNames = [];
    
        // Process file uploads
        if ($request->hasFile('product_image')) {
            foreach ($request->file('product_image') as $image) {
                $singleImageName = 'prod_' . time() . '_' . Str::random(6) . '.' . $image->extension();
                $image->move(public_path('product_images'), $singleImageName);
                $imageNames[] = $singleImageName;
            }
        }
    
        // Merge processed Base64 images
        $imageNames = array_merge($imageNames, $processedImages);
    
        // Create product with JSON encoded image paths
        $Product = Product::create(array_merge(
            $validator->validated(),
            ["product_image" => json_encode($imageNames)]
        ));
    
        return $this->Created($Product, "Product has been created");
    }
    
    /**
     * Processes Base64 image for products
     */
    protected function processProductImage($base64)
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            return false;
        }
    
        $imageData = substr($base64, strpos($base64, ',') + 1);
        $imageData = str_replace(' ', '+', $imageData);
        $decoded = base64_decode($imageData);
    
        if (!$decoded) {
            return false;
        }
    
        // Generate secure filename
        $filename = 'prod_' . time() . '_' . Str::random(8) . '.' . $type[1];
        $path = public_path('product_images/' . $filename);
    
        // Save with exclusive lock
        try {
            file_put_contents($path, $decoded, LOCK_EX);
            return $filename;
        } catch (\Exception $e) {
            Log::error("Product image save failed: " . $e->getMessage());
            return false;
        }
    }


    public function update(Request $request, string $id)
    {
        // Find the product
        $Product = Product::find($id);
        if (empty($Product)) {
            return $this->NotFound("Product not found");
        }

        $inputs = $request->all();
    
        // Sanitize product name if provided
        if (isset($inputs["name"])) {
            $inputs["name"] = $this->SanitizedName($inputs["name"]);
            if (empty($inputs["name"])) {
                unset($inputs["name"]);
            }
        }
    
        /* 
         * Image Processing Pipeline:
         * 1. Convert Base64 strings to files if present
         * 2. Keep existing file upload handling
         * 3. Preserve existing images if none provided
         */
        $existingImages = $Product->product_image ? json_decode($Product->product_image, true) : [];
        $newImages = [];
    
        if (isset($inputs['product_image']) && is_array($inputs['product_image'])) {
            foreach ($inputs['product_image'] as $key => $image) {
                if (is_string($image)) {
                    if ($image === '') {
                        continue; // Skip empty strings
                    } elseif (str_starts_with($image, 'data:image/')) {
                        // Process Base64 image
                        $filename = $this->processProductImage($image);
                        if (!$filename) {
                            return response()->json([
                                "ok" => false,
                                "errors" => ["product_image.$key" => ["Invalid Base64 image format"]],
                                "message" => "Validation Failed!"
                            ], 400);
                        }
                        $newImages[] = $filename;
                    } elseif (str_starts_with($image, 'images/')) {
                        // Keep existing image paths
                        $newImages[] = $image;
                    }
                } elseif ($request->hasFile("product_image.$key")) {
                    // Will be processed in validation
                    continue;
                }
            }
        }
    
        // Validation rules (supports both file objects and processed filenames)
        $validator = validator()->make($inputs, [
            "name" => "sometimes|string|unique:products,name," . $id,
            "product_image" => "sometimes|array",
            "product_image.*" => [
                function ($attribute, $value, $fail) {
                    if (
                        !($value instanceof UploadedFile) &&
                        !is_string($value)
                    ) {
                        $fail('Each product image must be a file or valid image string.');
                    }
                },
                'max:2048' // Applies to both files and Base64 decoded size
            ],
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
    
        // Process file uploads if any
        if ($request->hasFile('product_image')) {
            foreach ($request->file('product_image') as $uploadedImage) {
                $singleImageName = 'prod_' . time() . '_' . Str::random(6) . '.' . $uploadedImage->extension();
                $uploadedImage->move(public_path('product_images'), $singleImageName);
                $newImages[] = $singleImageName;
            }
        }
    
        // Determine final image array
        $finalImages = [];
        if (!empty($newImages)) {
            // Use new images (Base64 + file uploads)
            $finalImages = $newImages;
            
            // Delete old images that weren't preserved
            $this->cleanupOldImages($existingImages, $finalImages);
        } elseif (array_key_exists('product_image', $inputs)) {
            // Explicit empty array case
            $this->cleanupOldImages($existingImages, []);
        } else {
            // No image changes, keep existing
            $finalImages = $existingImages;
        }
    
        // Update product data
        $updateData = $validator->validated();
        if (!empty($finalImages)) {
            $updateData['product_image'] = json_encode($finalImages);
        }
    
        $Product->update($updateData);
    
        return $this->Ok($Product, "Product has been updated");
    }
    
    /**
     * Processes Base64 image for products
     */
    protected function updateProductImage($base64)
    {
        if (!preg_match('/^data:image\/(\w+);base64,/', $base64, $type)) {
            return false;
        }
    
        $imageData = substr($base64, strpos($base64, ',') + 1);
        $imageData = str_replace(' ', '+', $imageData);
        $decoded = base64_decode($imageData);
    
        if (!$decoded) {
            return false;
        }
    
        // Generate secure filename
        $filename = 'prod_' . time() . '_' . Str::random(8) . '.' . $type[1];
        $path = public_path('product_images/' . $filename);
    
        // Save with exclusive lock
        try {
            file_put_contents($path, $decoded, LOCK_EX);
            return $filename;
        } catch (\Exception $e) {
            Log::error("Product image save failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cleans up old images that are no longer needed
     */
    protected function cleanupOldImages(array $oldImages, array $newImages)
    {
        foreach ($oldImages as $oldImage) {
            if (!in_array($oldImage, $newImages)) {
                $path = public_path($oldImage);
                if (file_exists($path)) {
                    @unlink($path);
                }
            }
        }
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
