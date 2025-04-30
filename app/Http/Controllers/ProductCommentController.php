<?php

namespace App\Http\Controllers;

use App\Models\ProductComment;
use Illuminate\Http\Request;

class ProductCommentController extends Controller
{
    public function index() {
        $productId = request('product_id');
    
        if (!$productId) {
            return $this->BadRequest("Missing product_id");
        }
    
        $comments = ProductComment::with('user:id,username')
            ->where('product_id', $productId)
            ->get();
    
        if ($comments->isEmpty()) {
            return $this->NotFound("No comments found for the product: {$productId}");
        }
        
        return $this->Ok($comments);
    }
    

    public function show($productId, $commentId) {
        // Retrieve the specific comment with productId and commentId
        $Comment = ProductComment::with('user:id,username')
            ->where('product_id', $productId)
            ->where('id', $commentId)
            ->first();
    
        // Check if the comment exists
        if (empty($Comment)) {
            return $this->NotFound("Comment not found or has been deleted!");
        }
    
        return $this->Ok($Comment, "Successfully retrieved comment");
    }
    
//===================================================================
    // Additional feature by specific function:
    public function reviewsCount(){
        $totalReviews = ProductComment::count();

        return $this->Ok([
            'totalReviews' => $totalReviews,
        ], "Successfully retrieved reviews count");
    }

    public function aveRate()
    {
        $averageRate = round(ProductComment::avg('rating'), 1);
    
        return $this->Ok([
            'avarageRate' => $averageRate,
        ], "Retrieved Rate");
    }

    public function rateCount(Request $request)
    {
      
    $productId = $request->input('product_id');
    // $specificRating = $request->input('rating');
    // $totalRaters = ProductComment::where('product_id', $productId)->count();
    
    $counts = [];

    for ($rating = 1; $rating <= 5; $rating++) {
        $count = ProductComment::where('product_id', $productId)
            ->where('rating', $rating)
            ->count();

       
        $counts[$rating] = $count;
    }

    
    // $specificCount = ProductComment::where('product_id', $productId)
    //     ->where('rating', $specificRating)
    //     ->count();

    // $specificPercentage = $totalRaters > 0 ? ($specificCount / $totalRaters) * 100 : 0;

    // Return the results as JSON
    return response()->json([
        // 'Product ID' => $productId,
        // 'Specific Rating' => $specificRating,
        // 'Specific Count' => $specificCount,
        // 'Specific Percentage' => $specificPercentage,
        'rateCount' => $counts
    ], 200);
}
    
    

//====================================================================
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
