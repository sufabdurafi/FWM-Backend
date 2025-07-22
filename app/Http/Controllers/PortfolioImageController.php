<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortfolioImage;
use Illuminate\Http\Response;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class PortfolioImageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $images = PortfolioImage::all();
        return response()->json($images);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info('PortfolioImageController@store called', $request->all());
        $validated = $request->validate([
            'portfolio_id' => 'required|integer|exists:portfolios,id',
            'image' => 'required|file|image|max:5120', // max 5MB
        ]);

        if (!$request->hasFile('image')) {
            Log::error('No image uploaded');
            return response()->json(['error' => 'No image uploaded'], 400);
        }

        $file = $request->file('image');
        if (!$file->isValid()) {
            Log::error('Invalid image upload');
            return response()->json(['error' => 'Invalid image upload'], 400);
        }

        try {
            Log::info('Uploading to Cloudinary via SDK...');
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ],
            ]);
            $result = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                'folder' => 'portfolios'
            ]);
            $uploadedFileUrl = $result['secure_url'];
            Log::info('Cloudinary upload success', ['url' => $uploadedFileUrl]);
        } catch (\Exception $e) {
            Log::error('Cloudinary upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Cloudinary upload failed', 'message' => $e->getMessage()], 500);
        }

        $image = PortfolioImage::create([
            'portfolio_id' => $validated['portfolio_id'],
            'image_url' => $uploadedFileUrl,
        ]);
        Log::info('PortfolioImage created', ['id' => $image->id, 'url' => $uploadedFileUrl]);
        return response()->json($image, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $image = PortfolioImage::findOrFail($id);
        return response()->json($image);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $image = PortfolioImage::findOrFail($id);
        $validated = $request->validate([
            'portfolio_id' => 'sometimes|required|integer|exists:portfolios,id',
            'image_url' => 'sometimes|required|string',
        ]);
        $image->update($validated);
        return response()->json($image);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $image = PortfolioImage::findOrFail($id);
        $image->delete();
        return response()->json(['message' => 'Portfolio image deleted successfully.']);
    }
}
