<?php

namespace App\Http\Controllers;

use App\Models\BannerImage;
use Illuminate\Http\Request;

class BannerImageController extends Controller
{
    public function index()
    {
        // Fetch all banner images from the database
        $bannerImages = BannerImage::all();

        // Return the view with the banner images
        return view('banner_images.index', compact('bannerImages'));
    }

    public function show($id) {
        // Fetch the specific banner image by ID
        $bannerImage = BannerImage::findOrFail($id);

        // Return the view with the specific banner image
        return view('banner_images.show', compact('bannerImage'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,jfif|max:2048',
        ]);

        if (!$request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'No image was uploaded.'
            ], 400);
        }

        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '.' . $extension;
        $file->move(public_path('banner_images'), $fileName);

        $banner = BannerImage::create([
            'image' => 'banner_images/' . $fileName,
            'timestamp' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Banner image uploaded successfully.',
            'data' => [
                'filename' => $fileName,
                'path' => 'banner_images/' . $fileName,
                'full_url' => asset('banner_images/' . $fileName),
                'id' => $banner->id,
                'timestamp' => $banner->timestamp
            ]
        ]);
    }
}
