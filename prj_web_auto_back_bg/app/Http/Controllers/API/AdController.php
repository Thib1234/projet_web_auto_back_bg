<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Ad;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class AdController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Filtrage avancé
        $query = Ad::query();
        
        // Filtres basiques
        if ($request->has('brand')) {
            $query->where('brand', $request->brand);
        }
        
        if ($request->has('model')) {
            $query->where('model', $request->model);
        }
        
        if ($request->has('min_price') && $request->has('max_price')) {
            $query->whereBetween('price', [$request->min_price, $request->max_price]);
        }
        
        if ($request->has('min_year') && $request->has('max_year')) {
            $query->whereBetween('year', [$request->min_year, $request->max_year]);
        }
        
        if ($request->has('fuel_type')) {
            $query->where('fuel_type', $request->fuel_type);
        }
        
        // Pagination
        $ads = $query->with('photos')->latest()->paginate(10);
        
        return response()->json($ads);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validatedData = $request->validate([
        'brand' => 'required|string|max:255',
        'model' => 'required|string|max:255',
        'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        'mileage' => 'required|integer|min:0',
        'price' => 'required|numeric|min:0',
        'fuel_type' => 'required|string',
        'transmission' => 'required|string',
        'description' => 'required|string',
        'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
    ]);

    /** @var \App\Models\User $user */
    $user = Auth::user();

    $ad = $user->ads()->create($validatedData);

    if ($request->hasFile('photos')) {
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('ads', 'public');
            $ad->photos()->create(['path' => $path]);
        }
    }

    return response()->json($ad->load('photos'), 201);
}


    /**
     * Display the specified resource.
     */
    public function show(Ad $ad)
    {
        return response()->json($ad->load('photos', 'user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ad $ad)
    {
        // Vérifier si l'utilisateur est autorisé
        $this->authorize('update', $ad);

        $validatedData = $request->validate([
            'brand' => 'sometimes|string|max:255',
            'model' => 'sometimes|string|max:255',
            'year' => 'sometimes|integer|min:1900|max:' . (date('Y') + 1),
            'mileage' => 'sometimes|integer|min:0',
            'price' => 'sometimes|numeric|min:0',
            'fuel_type' => 'sometimes|string',
            'transmission' => 'sometimes|string',
            'description' => 'sometimes|string',
            'photos.*' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $ad->update($validatedData);

        // Traitement des photos si nécessaire
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $path = $photo->store('ads', 'public');
                $ad->photos()->create(['path' => $path]);
            }
        }

        return response()->json($ad->load('photos'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ad $ad)
    {
        // Vérifier si l'utilisateur est autorisé
        $this->authorize('delete', $ad);

        // Supprimer les photos associées du stockage
        foreach ($ad->photos as $photo) {
            Storage::disk('public')->delete($photo->path);
            $photo->delete();
        }

        $ad->delete();

        return response()->json(null, 204);
    }
    
    /**
     * Get trending/featured ads.
     */
    public function featured()
    {
        $featuredAds = Ad::with('photos')
            ->inRandomOrder()
            ->take(6)
            ->get();
            
        return response()->json($featuredAds);
    }
}