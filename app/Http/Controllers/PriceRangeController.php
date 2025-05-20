<?php

namespace App\Http\Controllers;

use App\Models\PriceRange;
use Illuminate\Http\Request;


/**
 * @OA\Get(
 *     path="/api/prices",
 *     summary="Get all price ranges",
 *     tags={"PriceRange"},
 *     @OA\Response(
 *         response=200,
 *         description="List of price ranges",
 *         @OA\JsonContent(
 *             @OA\Property(property="ok", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(ref="#/components/schemas/PriceRange")
 *             )
 *         )
 *     )
 * )
 *
 * @OA\Get(
 *     path="/api/prices/{id}",
 *     summary="Get a specific price range by ID",
 *     tags={"PriceRange"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Price range found",
 *         @OA\JsonContent(ref="#/components/schemas/PriceRange")
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Price range not found"
 *     )
 * )
 *
 * @OA\Post(
 *     path="/api/prices",
 *     summary="Create a new price range",
 *     tags={"PriceRange"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"price"},
 *             @OA\Property(property="price", type="integer", example=1000),
 *             @OA\Property(property="timestamp", type="string", example="2024-01-01 12:00:00")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Price range created"
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error"
 *     )
 * )
 *
 * @OA\Patch(
 *     path="/api/prices/{id}",
 *     summary="Update a price range",
 *     tags={"PriceRange"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="price", type="integer", example=1500),
 *             @OA\Property(property="timestamp", type="string", example="2024-01-01 12:00:00")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Price range updated"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Price range not found"
 *     )
 * )
 *
 * @OA\Delete(
 *     path="/api/prices/{id}",
 *     summary="Delete a price range",
 *     tags={"PriceRange"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Price range deleted"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Price range not found"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="PriceRange",
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="price", type="integer"),
 *     @OA\Property(property="timestamp", type="string", format="date-time")
 * )
 */
class PriceRangeController extends Controller
{
    // Display a list of all price ranges
    public function index()
    {
        $priceRanges = PriceRange::all();
        return response()->json([
            "ok" => true,
            "message" => "List of price was retrieved successfully.",
            "data" => $priceRanges
        ]);
    }

    public function show($id) {
        // Fetch the specific banner image by ID
        $priceRange = PriceRange::find($id);

        if (!$priceRange) {
            return  $this->NotFound("Price to this item Not found!");
        }

        // Return the view with the specific banner image
        return response()->json([
            "ok" => true,
            "message" => "Price to this item was retrieved successfully.",
            "data" => $priceRange
        ]);
    }

    // Store a new price range
    public function store(Request $request)
    {
        $validated = $request->validate([
            'price' => 'required|integer',
            'timestamp' => 'nullable|string',
        ]);

        $priceRange = PriceRange::create($validated);
        return $this->Ok($priceRange, "Price Inserted");
    }

    // Update a specific price range
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'price' => 'sometimes|required|integer',
            'timestamp' => 'nullable|string',
        ]);

        $priceRange = PriceRange::findOrFail($id);
        $priceRange->update($validated);

        return $this->Ok($priceRange, "Price Updated");
    }

    // Delete a specific price range
    public function destroy($id)
    {
        $priceRange = PriceRange::findOrFail($id);
        $priceRange->delete();

        return $this->Ok("Price Deleted");
    }
}
