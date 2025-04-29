<?php

namespace App\Http\Controllers;

use App\Models\ProductComment;
use Illuminate\Http\Request;

class ProductCommentController extends Controller
{
    public function index($productId) {
        $comments = ProductComment::with('user:id,username')
        ->where('product_id', $productId)
        ->get();

        return $this->Ok($comments);
    }

    public function show($productId, $commentId) {
        $Comment = ProductComment::find($commentId);


        if(empty($Comment)) {
            return $this->NotFound("Comment not found or has been deleted!");
        }
        
        $Comment = ProductComment::with('user:id,username');
        // ->where('product_id', $productId)
        // ->where('id', $commentId)
        // ->first();


        return $this->Ok($Comment, "Successfully retrieved comment");

    }

    public function reviewsCount(){
        $totalReviews = ProductComment::count();
        $totalSumReviews = ProductComment::sum('id');

        return $this->Ok([
            'totalReviews' => $totalReviews,
            'totalSumReviews' => $totalSumReviews
        ], "Successfully retrieved reviews count");
    }

    public function store(Request $request) {
        $inputs = $request->all();

        $validator = validator()->make($inputs, [
            "product_id" => "required|exists:products,id|integer",
            "user_id" => "required|exists:users,id|integer",
            "comment" => "required|string",
            "rating" => "required|integer|min:1|max:5",
            "comment_id" => "nullable|exists:product_comments,id|integer"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        $Comment = ProductComment::create($validator->validated());

        return $this->Ok($Comment, "Comment has been created");
    }

    public function update(Request $request, string $id) {
        $inputs = $request->all();

        $Comment = ProductComment::find($id);

        if(empty($Comment)) {
            return $this->NotFound("Comment not found");
        }

        $validator = validator()->make($inputs, [
            "product_id" => "sometimes|exists:products,id|integer",
            "user_id" => "sometimes|exists:users,id|integer",
            "comment" => "sometimes|string",
            "rating" => "sometimes|integer|min:1|max:5",
            "comment_id" => "nullable|exists:product_comments,id|integer"
        ]);

        if ($validator->fails()) {
            return $this->BadRequest($validator->errors());
        }

        $Comment->update($validator->validated());

        return $this->Ok($Comment, "Comment has been updated");
    }

    public function destroy(string $id) {
        $Comment = ProductComment::find($id);

        if(empty($Comment)) {
            return $this->NotFound("Comment not found");
        }

        $Comment->delete();

        return $this->Ok($Comment, "Comment has been deleted");
    }
}
