<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class FavoriteController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $favorites = $request->user()->favorites()->with('ad.photos')->get();
        return response()->json($favorites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ad_id' => 'required|exists:ads,id',
        ]);

        // Vérifier si l'annonce est déjà dans les favoris
        $existingFavorite = $request->user()->favorites()->where('ad_id', $validatedData['ad_id'])->first();
        
        if ($existingFavorite) {
            return response()->json(['message' => 'Cette annonce est déjà dans vos favoris'], 422);
        }
        
        $favorite = $request->user()->favorites()->create([
            'ad_id' => $validatedData['ad_id'],
        ]);
        
        return response()->json($favorite->load('ad'), 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Favorite $favorite)
    {
        // Vérifier si l'utilisateur est autorisé
        $this->authorize('delete', $favorite);
        
        $favorite->delete();
        
        return response()->json(null, 204);
    }
    
    /**
     * Toggle favorite status for an ad.
     */
    public function toggle(Request $request)
    {
        $validatedData = $request->validate([
            'ad_id' => 'required|exists:ads,id',
        ]);
        
        $existingFavorite = $request->user()->favorites()->where('ad_id', $validatedData['ad_id'])->first();
        
        if ($existingFavorite) {
            $existingFavorite->delete();
            return response()->json(['status' => 'removed']);
        } else {
            $favorite = $request->user()->favorites()->create([
                'ad_id' => $validatedData['ad_id'],
            ]);
            return response()->json(['status' => 'added', 'favorite' => $favorite]);
        }
    }
}