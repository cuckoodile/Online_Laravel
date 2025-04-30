<?php

namespace App\Http\Controllers;
use App\Models\ProductSpecification;

use Illuminate\Http\Request;

class ProductSpecificationController extends Controller
{
    public function index() {
        $productSpecifications = ProductSpecification::with('product')->get();

        return $this->Ok($productSpecifications);
    }

    public function show(string $id) {
        $productSpecification = ProductSpecification::find($id);

        if(empty($productSpecification)) {
            return $this->NotFound("Product Specification not found");
        }

        $productSpecification->product;

        return $this->Ok($productSpecification);
    }

    public function store(Request $request) {
        $inputs = $request->all();

        $validator = validator()->make($inputs, [
            "product_id" => "required|exists:products,id|integer",
            "details" => "required|array",
            // "details.*" => "string",
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $productSpecification = ProductSpecification::create($validator->validated());

        return $this->Ok($productSpecification, "Product Specification has been created");
    }

    public function update(Request $request, string $id) {
        $inputs = $request->all();

        $productSpecification = ProductSpecification::find($id);

        if(empty($productSpecification)) {
            return $this->NotFound("Product Specification not found");
        }

        $validator = validator()->make($inputs, [
            "product_id" => "sometimes|exists:products,id|integer",
            "details" => "sometimes|array",
            "details.*" => "string",
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator);
        }

        $productSpecification->update($validator->validated());

        return $this->Ok($productSpecification, "Product Specification has been updated");
    }

    public function destroy(string $id) {
        $productSpecification = ProductSpecification::find($id);

        if(empty($productSpecification)) {
            return $this->NotFound("Product Specification not found");
        }

        $productSpecification->delete();

        return $this->Ok(null, "Product Specification has been deleted");
    }
}