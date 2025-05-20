<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;


/**
 * @OA\Get(
 *     path="/api/categories",
 *     summary="Get all categories",
 *     tags={"Category"},
 *     @OA\Response(
 *         response=200,
 *         description="List of categories",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/Category")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/categories/{id}",
 *     summary="Get a specific category by ID",
 *     tags={"Category"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category found",
 *         @OA\JsonContent(ref="#/components/schemas/Category")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/categories",
 *     summary="Create a new category",
 *     tags={"Category"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Electronics")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Category created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/categories/{id}",
 *     summary="Update a category",
 *     tags={"Category"},
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
 *             required={"name"},
 *             @OA\Property(property="name", type="string", example="Updated Category")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/categories/{id}",
 *     summary="Delete a category",
 *     tags={"Category"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Category deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Category not found"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="Category",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="name", type="string"),
 *     @OA\Property(
 *         property="products",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/Product")
 *     )
 * )
 */
class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::with('products')->get();
        
        return $this->Ok($categories);
    } 

    public function show(string $id)
    {
        $category = Category::find($id);
        
        if (empty($category)) {
            return $this->NotFound("Category not found");
        }

        $category->products;

        return $this->Ok($category);
    }

    public function store (Request $request)
    {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"]);

        $validator = validator()->make($request->all(), [
            "name" => "required|string|unique:categories"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator, "BOBO!");
        }

        $category = Category::create($validator->validated());

        return $this->Created($category, "Category has been created");
    }


    public function update (Request $request, string $id) {
        $inputs = $request->all();

        $inputs["name"] = $this->SanitizedName($inputs["name"] ?? "");

        $validator = validator()->make($request->all(), [
            "name" => "required|string|unique:categories,name,$id"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        $category = Category::find($id);

        if (empty($category)) {
            return $this->NotFound("Category not found");
        }

        $category->update($validator->validated());

        return $this->Ok($category, "Category has been updated");
        
    }

    public function destroy(string $id)
    {
        $category = Category::find($id);

        if (empty($category)) { 
            return $this->NotFound("Category not found");
        }

        $category->delete();

        return $this->Ok(null, "Category has been deleted");
    }

}
