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
        return response()->json($priceRanges);
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

    // Show a specific price range
    public function show($id)
    {
        $priceRange = PriceRange::findOrFail($id);
        return response()->json($priceRange);
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
