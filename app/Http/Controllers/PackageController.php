<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Package;
use Illuminate\Http\Response;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // if (Auth::user()->role !== 'admin') {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        $packages = Package::all();
        return response()->json($packages);
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
        // if (Auth::user()->role !== 'admin') {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'features' => 'nullable',
        ]);
        $package = Package::create($validated);
        return response()->json($package, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // if (Auth::user()->role !== 'admin') {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        $package = Package::findOrFail($id);
        return response()->json($package);
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
        // if (Auth::user()->role !== 'admin') {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        $package = Package::findOrFail($id);
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'features' => 'nullable',
        ]);
        $package->update($validated);
        return response()->json($package);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        // if (Auth::user()->role !== 'admin') {
        //     return response()->json(['error' => 'Forbidden'], 403);
        // }
        $package = Package::findOrFail($id);
        $package->delete();
        return response()->json(['message' => 'Package deleted successfully.']);
    }
}
