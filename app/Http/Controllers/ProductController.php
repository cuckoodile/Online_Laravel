<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::with('category')->get();
        
        return $this->Ok($products);
    } 

    public function show(string $id)
    {
        $Product = Product::find($id);
        
        if (empty($Product)) {
            return $this->NotFound("Product not found");
        }

        $Product->category;

        return $this->Ok($Product);
    }


    public function store (Request $request)
    {
        $inputs = $request->all();

        // Sanitize name
        $inputs["name"] = $this->SanitizedName($inputs["name"]);

        $validator = validator()->make($inputs, [
            "name" => "required|string|unique:products",
            "price" => "required|numeric",
            "description" => "required|string",
            "stock" => "required|integer",
            "category_id" => "required|exists:categories,id|integer"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $Product = Product::create($validator->validated());

        return $this->Created($Product, "Product has been created");
    }


    public function update (Request $request, string $id) {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");
        if (empty($inputs["name"])) {
            unset($inputs["name"]);
        }

        $validator = validator()->make($request->all(), [
            "name" => "sometimes|string|unique:products",
            "price" => "sometimes|numeric",
            "description" => "sometimes|string",
            "stock" => "sometimes|integer",
            "category_id" => "sometimes|exists:categories,id|integer"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $Product = Product::find($id);

        if (empty($Product)) {
            return $this->NotFound("Product not found");
        }

        $Product->update($validator->validated());

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
