<?php

namespace App\Http\Controllers;

use App\Models\PriceRange;
use Illuminate\Http\Request;

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
