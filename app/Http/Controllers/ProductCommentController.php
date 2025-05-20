<?php

namespace App\Http\Controllers;

use App\Models\ProductComment;
use Illuminate\Http\Request;


/**
 * @OA\Get(
 *     path="/api/comments",
 *     summary="Get all comments for a product",
 *     tags={"ProductComment"},
 *     @OA\Parameter(
 *         name="product_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="List of comments",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/ProductComment")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Missing product_id"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="No comments found for the product"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/comments/{productId}/{commentId}",
 *     summary="Get a specific comment by product and comment ID",
 *     tags={"ProductComment"},
 *     @OA\Parameter(
 *         name="productId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Parameter(
 *         name="commentId",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment found",
 *         @OA\JsonContent(ref="#/components/schemas/ProductComment")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Comment not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/comments",
 *     summary="Create a new comment for a product",
 *     tags={"ProductComment"},
 *     security={{"sanctum":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"product_id","user_id","comment","rating"},
 *             @OA\Property(property="product_id", type="integer", example=1),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="comment", type="string", example="Great product!"),
 *             @OA\Property(property="rating", type="integer", example=5),
 *             @OA\Property(property="comment_id", type="integer", nullable=true, example=null)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Comment created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/comments/{id}",
 *     summary="Update a comment",
 *     tags={"ProductComment"},
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
 *             @OA\Property(property="product_id", type="integer"),
 *             @OA\Property(property="user_id", type="integer"),
 *             @OA\Property(property="comment", type="string"),
 *             @OA\Property(property="rating", type="integer"),
 *             @OA\Property(property="comment_id", type="integer", nullable=true)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Comment not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/comments/{id}",
 *     summary="Delete a comment",
 *     tags={"ProductComment"},
 *     security={{"sanctum":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Comment deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Comment not found"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/reviewsCount",
 *     summary="Get total number of reviews",
 *     tags={"ProductComment"},
 *     @OA\Response(
 *         response=200,
 *         description="Total reviews count"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/aveRate",
 *     summary="Get average rating for all products",
 *     tags={"ProductComment"},
 *     @OA\Response(
 *         response=200,
 *         description="Average rating"
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/rateCount",
 *     summary="Get count of each rating value for a product",
 *     tags={"ProductComment"},
 *     @OA\Parameter(
 *         name="product_id",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Rating counts"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ProductComment",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="product_id", type="integer"),
 *     @OA\Property(property="user_id", type="integer"),
 *     @OA\Property(property="comment", type="string"),
 *     @OA\Property(property="rating", type="integer"),
 *     @OA\Property(property="comment_id", type="integer", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
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
        $counts[$rating] = ProductComment::where('product_id', $productId)
            ->where('rating', $rating)
            ->count();

       
        // $counts[$rating] = $count;
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
