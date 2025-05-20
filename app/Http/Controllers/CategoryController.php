<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

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
