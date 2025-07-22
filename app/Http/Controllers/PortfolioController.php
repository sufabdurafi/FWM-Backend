<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolio;
use Illuminate\Http\Response;

class PortfolioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $portfolios = Portfolio::with(['images', 'videos'])->get();
        return response()->json($portfolios);
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $portfolio = Portfolio::create($validated);
        return response()->json($portfolio, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $portfolio = Portfolio::with(['images', 'videos'])->findOrFail($id);
        return response()->json($portfolio);
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
        $portfolio = Portfolio::findOrFail($id);
        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);
        $portfolio->update($validated);
        return response()->json($portfolio);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $portfolio = Portfolio::findOrFail($id);
        $portfolio->delete();
        return response()->json(['message' => 'Portfolio deleted successfully.']);
    }
}
