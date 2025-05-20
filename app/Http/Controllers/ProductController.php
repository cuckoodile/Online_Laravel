<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductSpecification;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

/**
 * @OA\Get(
 *     path="/api/products",
 *     summary="Get all products",
 *     tags={"Product"},
 *     @OA\Response(
 *         response=200,
 *         description="List of products",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Product")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/products/{id}",
 *     summary="Get a specific product by ID",
 *     tags={"Product"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product found",
 *         @OA\JsonContent(ref="#/components/schemas/Product")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/products",
 *     summary="Create a new product",
 *     tags={"Product"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name","product_image","price","description","category_id","product_specifications","stock"},
 *             @OA\Property(property="name", type="string", example="Sample Product"),
 *             @OA\Property(property="product_image", type="array", @OA\Items(type="string", format="binary")),
 *             @OA\Property(property="price", type="number", format="float", example=99.99),
 *             @OA\Property(property="description", type="string", example="Product description"),
 *             @OA\Property(property="category_id", type="integer", example=1),
 *             @OA\Property(
 *                 property="product_specifications",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="details", type="array", @OA\Items(type="string"))
 *                 )
 *             ),
 *             @OA\Property(property="stock", type="integer", example=10)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Product created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/products/{id}",
 *     summary="Update a product",
 *     tags={"Product"},
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
 *             @OA\Property(property="name", type="string"),
 *             @OA\Property(property="product_image", type="array", @OA\Items(type="string", format="binary")),
 *             @OA\Property(property="price", type="number", format="float"),
 *             @OA\Property(property="description", type="string"),
 *             @OA\Property(property="category_id", type="integer"),
 *             @OA\Property(
 *                 property="product_specifications",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="details", type="array", @OA\Items(type="string"))
 *                 )
 *             ),
 *             @OA\Property(property="stock", type="integer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/products/{id}",
 *     summary="Delete a product",
 *     tags={"Product"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product not found"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Product",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(property="product_image", type="array", @OA\Items(type="string")),
 *     @OA\Property(property="price", type="number", format="float"),
 *     @OA\Property(property="description", type="string"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="product_specifications", type="array", @OA\Items(type="object")),
 *     @OA\Property(property="stock", type="integer"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();

        foreach ($products as $product) {
            $product->product_specifications;
            // Interpolate full URLs for each product image
            $images = $product->product_image;
            if (is_string($images)) {
                $images = json_decode($images, true);
            }
            $product->product_image = array_map(function ($img) {
                return "http://127.0.0.1:8000/{$img}";
            }, $images ?? []);

            // On deployment
            // $product->product_image = array_map(function ($img) {
            //     return "https://apidevsixtech.styxhydra.com/{$img}";
            // }, $images ?? []);

            $product->category;
            $product->product_comments;
            // Calculate and include stock
            $inboundStock = $product->transactions()->where('type_id', 1)->sum('product_transaction.quantity');
            $outboundStock = $product->transactions()->where('type_id', 2)->sum('product_transaction.quantity');
            $product->stock = $inboundStock - $outboundStock;
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
        // Interpolate full URLs for each product image
        $images = $Product->product_image;
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
        $Product->product_image = array_map(function ($img) {
            return "http://127.0.0.1:8000/{$img}";
        }, $images ?? []);
        $Product->category;
        $Product->product_comments;

        return $this->Ok($Product);
    }

    public function getProductImage(string $id)
    {
        $Product = Product::find($id); // if you put findOrFail it will automatically return 404 error if product not found

        if (!$Product) {
            return $this->NotFound("Product Image not found"); // so this may not need to be used
        }

        $images = $Product->product_image;
        if (is_string($images)) {
            $images = json_decode($images, true);
        }
        $imageUrls = collect($images)->map(function ($image) {
            return asset($image);
        });

        return $this->Ok($imageUrls);
    }


    public function store(Request $request)
    {
        // Validate the input
        $request->validate([
            "name" => "required|string|unique:products",
            "product_image" => "required|array|min:1|max:4",
            "product_image.*" => "required|image|mimes:jpeg,png,jpg,gif,webp,jfif",
            "price" => "required|numeric",
            "description" => "required|string",
            "category_id" => "required|exists:categories,id",
            "product_specifications" => "required|array|min:2|max:4",
            "stock" => "required|numeric|min:1"
        ]);


        // Sanitize product name
        $name = $this->SanitizedName($request->name);

        $category = Category::find($request->category_id);
        if (!$category) {
            return $this->NotFound("Category not found");
        }

        // Upload images and collect paths
        $imagePaths = [];

        $categoryName = $category ? $category->name : 'Uncategorized';
        $categoryFolder = Str::slug($categoryName);

        // Iterated storing of images
        foreach ($request->file('product_image') as $file) {
            $extension = $file->getClientOriginalExtension();
            $fileName = time() . '_' . uniqid() . '.' . $extension;
            $destinationPath = public_path("assets/media/$categoryFolder");
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0755, true);
            }
            $file->move($destinationPath, $fileName);
            $imagePaths[] = "assets/media/{$categoryFolder}/{$fileName}";
        }

        $request->product_image = $imagePaths;

        $product = Product::create([
            'name' => $name,
            'price' => $request->price,
            'admin_id' => $request->user()->id,
            'product_image' => json_encode($imagePaths),
            'description' => $request->description,
            'category_id' => $request->category_id,
        ]);

        // Save specifications (accept key-value pairs)
        if (is_array($request->product_specifications)) {
            foreach ($request->product_specifications as $key => $value) {
                $productSpecification = [
                    'product_id' => $product->id,
                    'details' => json_encode([$key => $value]),
                ];
                ProductSpecification::create($productSpecification);
            }
        }

        $transaction = Transaction::create([
            'user_id' => $request->user()->id,
            'type_id' => 1, // Inbound transaction type
            'status_id' => 1, // Default status (e.g., Pending)
            'payment_method_id' => 1, // Default payment method (e.g., Cash)
            'address_id' => 1, // Default address (can be adjusted as needed)
        ]);

        $transaction->products()->attach($product->id, [
            'quantity' => $request->stock,
            'price' => $request->price,
            'sub_total' => $request->stock * $request->price,
        ]);

        // Calculate stock based on inbound and outbound transactions
        $inboundStock = $product->transactions()->where('type_id', 1)->sum('product_transaction.quantity');
        $outboundStock = $product->transactions()->where('type_id', 2)->sum('product_transaction.quantity');
        $stock = $inboundStock - $outboundStock;
        $product->stock = $stock;

        return $this->Created($product, "Product has been created with specifications and stock!");
    }



    public function update(Request $request, string $id)
    {
        $product = Product::find($id);

        // Define validation rules for all possible fields
        $validator = validator()->make($request->all(), [
            "name" => "sometimes|string|unique:products,name,$id",
            "product_image" => "sometimes|array|min:1|max:10",
            "product_image.*" => "sometimes|image|mimes:jpeg,png,jpg,gif,webp,jfif",
            "price" => "sometimes|numeric",
            "description" => "sometimes|string",
            "category_id" => "sometimes|exists:categories,$id",
            "product_specifications" => "sometimes|array|min:2|max:10",
            "product_specifications.*.details" => "sometimes|array",
            "stock" => "sometimes|numeric|min:0"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        // Update name if provided
        if ($request->has('name')) {
            $request->name = $this->SanitizedName($request->name);
        }

        // Update images if provided
        if ($request->hasFile('product_image')) {
            $imagePaths = [];
            foreach ($request->file('product_image') as $file) {
                $extension = $file->getClientOriginalExtension();
                $fileName = time() . '_' . uniqid() . '.' . $extension;
                $file->move(public_path('product_images'), $fileName);
                $imagePaths[] = "product_images/$fileName";
            }

            // Delete old images if they exist
            if (is_array($request->product_image)) {
                foreach ($request->product_image as $oldImage) {
                    if (file_exists(public_path($oldImage))) {
                        unlink(public_path($oldImage));
                    }
                }
            }

            $request->product_image = $imagePaths;
        }

        // Update other simple fields if provided
        $simpleFields = ['price', 'description', 'category_id'];
        foreach ($simpleFields as $field) {
            if ($request->has($field)) {
                $request->$field = $request->$field;
            }
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

        $product->update($validator->validated());

        // Return the updated product   
        return $this->Ok($request, "Product ID: $request->id has been updated successfully!");
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
