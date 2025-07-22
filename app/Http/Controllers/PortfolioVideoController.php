<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortfolioVideo;
use Illuminate\Http\Response;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class PortfolioVideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $videos = PortfolioVideo::all();
        return response()->json($videos);
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
        $validated = $request->validate([
            'portfolio_id' => 'required|integer|exists:portfolios,id',
            'video' => 'required|file|mimetypes:video/mp4,video/avi,video/mpeg,video/quicktime|max:51200', // max 50MB
        ]);

        // Upload ke Cloudinary via SDK
        try {
            $cloudinary = new Cloudinary([
                'cloud' => [
                    'cloud_name' => config('services.cloudinary.cloud_name'),
                    'api_key' => config('services.cloudinary.api_key'),
                    'api_secret' => config('services.cloudinary.api_secret'),
                ],
            ]);
            $result = $cloudinary->uploadApi()->upload($request->file('video')->getRealPath(), [
                'folder' => 'portfolios',
                'resource_type' => 'video',
            ]);
            $uploadedFileUrl = $result['secure_url'];
        } catch (\Exception $e) {
            Log::error('Cloudinary video upload failed: ' . $e->getMessage());
            return response()->json(['error' => 'Cloudinary video upload failed', 'message' => $e->getMessage()], 500);
        }

        $video = PortfolioVideo::create([
            'portfolio_id' => $validated['portfolio_id'],
            'video_url' => $uploadedFileUrl,
        ]);
        return response()->json($video, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $video = PortfolioVideo::findOrFail($id);
        return response()->json($video);
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
        $video = PortfolioVideo::findOrFail($id);
        $validated = $request->validate([
            'portfolio_id' => 'sometimes|required|integer|exists:portfolios,id',
            'video_url' => 'sometimes|required|string',
        ]);
        $video->update($validated);
        return response()->json($video);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $video = PortfolioVideo::findOrFail($id);
        $video->delete();
        return response()->json(['message' => 'Portfolio video deleted successfully.']);
    }
}
