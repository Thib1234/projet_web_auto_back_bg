<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CartItem;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class CartItemController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $cartItems = $request->user()->cartItems()->with('ad.photos')->get();
        return response()->json($cartItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ad_id' => 'required|exists:ads,id',
        ]);

        // Vérifier si l'annonce existe et n'est pas déjà dans le panier
        $ad = Ad::findOrFail($validatedData['ad_id']);
        
        $existingItem = $request->user()->cartItems()->where('ad_id', $ad->id)->first();
        
        if ($existingItem) {
            return response()->json(['message' => 'Cette annonce est déjà dans votre panier'], 422);
        }
        
        $cartItem = $request->user()->cartItems()->create([
            'ad_id' => $ad->id,
        ]);
        
        return response()->json($cartItem->load('ad'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(CartItem $cartItem)
    {
        // Vérifier si l'utilisateur est autorisé
        $this->authorize('view', $cartItem);
        
        return response()->json($cartItem->load('ad'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CartItem $cartItem)
    {
        // Vérifier si l'utilisateur est autorisé
        $this->authorize('delete', $cartItem);
        
        $cartItem->delete();
        
        return response()->json(null, 204);
    }
    
    /**
     * Clear all items from the cart.
     */
    public function clear(Request $request)
    {
        $request->user()->cartItems()->delete();
        
        return response()->json(['message' => 'Panier vidé avec succès']);
    }
}