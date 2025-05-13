<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class TransactionController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Les admins peuvent voir toutes les transactions
        if ($request->user()->hasRole('admin')) {
            $transactions = Transaction::with('user', 'ad')->latest()->paginate(15);
        } else {
            // Les utilisateurs normaux ne voient que leurs transactions
            $transactions = $request->user()->transactions()->with('ad')->latest()->paginate(15);
        }
        
        return response()->json($transactions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'ad_id' => 'required|exists:ads,id',
            'payment_method' => 'required|string',
            'stripe_token' => 'required_if:payment_method,stripe',
        ]);
        
        // Récupérer l'annonce
        $ad = \App\Models\Ad::findOrFail($validatedData['ad_id']);
        
        // Vérifier si l'annonce est disponible (pas déjà vendue)
        if ($ad->status !== 'available') {
            return response()->json(['message' => 'Cette voiture n\'est plus disponible'], 422);
        }
        
        // Simuler un processus de paiement avec Stripe
        if ($validatedData['payment_method'] === 'stripe') {
            // Dans un vrai scénario, on ferait appel à l'API Stripe ici
            // Stripe::charges()->create([...])
            
            // Pour la simulation, on considère que le paiement est réussi
        }
        
        // Créer la transaction
        $transaction = $request->user()->transactions()->create([
            'ad_id' => $ad->id,
            'amount' => $ad->price,
            'payment_method' => $validatedData['payment_method'],
            'status' => 'completed', // Dans un vrai scénario, ce statut dépendrait de la réponse de Stripe
        ]);
        
        // Marquer l'annonce comme vendue
        $ad->update(['status' => 'sold']);
        
        // Supprimer l'annonce du panier de tous les utilisateurs
        CartItem::where('ad_id', $ad->id)->delete();
        
        return response()->json($transaction->load('ad', 'user'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Transaction $transaction)
    {
        // Vérifier si l'utilisateur est autorisé
        $this->authorize('view', $transaction);
        
        return response()->json($transaction->load('ad', 'user'));
    }
    
    /**
     * Process a cart checkout.
     */
    public function checkout(Request $request)
    {
        $validatedData = $request->validate([
            'payment_method' => 'required|string',
            'stripe_token' => 'required_if:payment_method,stripe',
        ]);
        
        $user = $request->user();
        $cartItems = $user->cartItems()->with('ad')->get();
        
        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Votre panier est vide'], 422);
        }
        
        $transactions = [];
        
        foreach ($cartItems as $cartItem) {
            $ad = $cartItem->ad;
            
            // Vérifier si l'annonce est toujours disponible
            if ($ad->status !== 'available') {
                continue;
            }
            
            // Simuler un processus de paiement
            if ($validatedData['payment_method'] === 'stripe') {
                // Dans un vrai scénario, appel API Stripe ici
            }
            
            // Créer la transaction
            $transaction = $user->transactions()->create([
                'ad_id' => $ad->id,
                'amount' => $ad->price,
                'payment_method' => $validatedData['payment_method'],
                'status' => 'completed',
            ]);
            
            // Marquer l'annonce comme vendue
            $ad->update(['status' => 'sold']);
            
            $transactions[] = $transaction;
            
            // Supprimer l'élément du panier
            $cartItem->delete();
        }
        
        // Supprimer tous les éléments du panier associés aux annonces qui ont été vendues
        CartItem::whereIn('ad_id', collect($transactions)->pluck('ad_id')->toArray())->delete();
        
        return response()->json([
            'message' => 'Paiement traité avec succès',
            'transactions' => $transactions
        ], 201);
    }
}