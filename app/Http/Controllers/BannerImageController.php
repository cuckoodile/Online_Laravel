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

        if(!$bannerImages) {
            return $this->NotFound("No image found!");
        }
        
        // Return the view with the banner images
        return response()->json([
            "ok" => true,
            "message" => "List of images was retrieved successfully.",
            "data" => $bannerImages
        ]);
    }

    public function show($id) {
        // Fetch the specific banner image by ID
        $bannerImage = BannerImage::find($id);

        if (!$bannerImage) {
            return  $this->NotFound("No image found!");
        }

        // Return the view with the specific banner image
        return response()->json([
            "ok" => true,
            "message" => "Image was retrieved successfully.",
            "data" => $bannerImage
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,jfif|max:2048'
        ]);

        if (!$request->hasFile('image')) {
            return response()->json([
                'ok' => false,
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
            'ok' => true,
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

    public function update(Request $request, $id)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp,jfif|max:2048',
        ]);

        // Find the banner image to update
        $bannerImage = BannerImage::findOrFail($id);

        // Delete the old image file if it exists
        if (file_exists(public_path($bannerImage->image))) {
            unlink(public_path($bannerImage->image));
        }

        // Upload the new image
        $file = $request->file('image');
        $extension = $file->getClientOriginalExtension();
        $fileName = time() . '.' . $extension;
        $file->move(public_path('banner_images'), $fileName);

        // Update the banner image record
        $bannerImage->update([
            'image' => 'banner_images/' . $fileName,
            'timestamp' => now(),
        ]);

        return response()->json([
            'ok' => true,
            'message' => 'Banner image updated successfully.',
            'data' => [
                'filename' => $fileName,
                'path' => 'banner_images/' . $fileName,
                'full_url' => asset('banner_images/' . $fileName),
                'id' => $bannerImage->id,
                'timestamp' => $bannerImage->timestamp
            ]
        ]);
    }

    public function destroy($id)
    {
        // Find the banner image to delete
        $bannerImage = BannerImage::findOrFail($id);

        // Delete the image file if it exists
        if (file_exists(public_path($bannerImage->image))) {
            unlink(public_path($bannerImage->image));
        }

        // Delete the record from database
        $bannerImage->delete();

        return response()->json([
            'ok' => true,
            'message' => 'Banner image deleted successfully.'
        ]);
    }
}
