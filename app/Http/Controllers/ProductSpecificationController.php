<?php

namespace App\Http\Controllers;
use App\Models\ProductSpecification;

use Illuminate\Http\Request;


/**
 * @OA\Get(
 *     path="/api/specifications",
 *     summary="Get all product specifications",
 *     tags={"ProductSpecification"},
 *     @OA\Response(
 *         response=200,
 *         description="List of product specifications",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/ProductSpecification")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/specifications/{id}",
 *     summary="Get a specific product specification by ID",
 *     tags={"ProductSpecification"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product specification found",
 *         @OA\JsonContent(ref="#/components/schemas/ProductSpecification")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product Specification not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/specifications",
 *     summary="Create a new product specification",
 *     tags={"ProductSpecification"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_id","details"},
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(
 *                 property="details",
 *                 type="array",
 *                 @OA\Items(type="string", example="Color: Red")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product Specification has been created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/specifications/{id}",
 *     summary="Update a product specification",
 *     tags={"ProductSpecification"},
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
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(
 *                 property="details",
 *                 type="array",
 *                 @OA\Items(type="string", example="Color: Blue")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product Specification has been updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product Specification not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/specifications/{id}",
 *     summary="Delete a product specification",
 *     tags={"ProductSpecification"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Product Specification has been deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Product Specification not found"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ProductSpecification",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(
 *         property="details",
 *         type="array",
 *         @OA\Items(type="string")
 *     ),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
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
            "details.*" => "string",
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