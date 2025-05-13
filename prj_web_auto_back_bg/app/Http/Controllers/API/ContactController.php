<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;


class ContactController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Réservé aux admins
        $this->authorize('viewAny', Contact::class);
        
        $contacts = Contact::latest()->paginate(15);
        return response()->json($contacts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'g-recaptcha-response' => 'required|captcha', // Nécessite l'installation d'un package de captcha
        ]);

        $contact = Contact::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'subject' => $validatedData['subject'],
            'message' => $validatedData['message'],
        ]);

        return response()->json([
            'message' => 'Votre message a été envoyé avec succès',
            'contact' => $contact
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        // Réservé aux admins
        $this->authorize('view', $contact);
        
        return response()->json($contact);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Contact $contact)
    {
        // Réservé aux admins
        $this->authorize('update', $contact);
        
        $validatedData = $request->validate([
            'status' => 'required|string|in:new,read,replied',
            'admin_notes' => 'nullable|string',
        ]);

        $contact->update($validatedData);
        
        return response()->json($contact);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        // Réservé aux admins
        $this->authorize('delete', $contact);
        
        $contact->delete();
        
        return response()->json(null, 204);
    }
}