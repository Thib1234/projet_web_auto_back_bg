<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Photo;
use App\Models\Ad;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class PhotoController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, Ad $ad)
    {
        $photos = $ad->photos;
        return response()->json($photos);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Ad $ad)
    {
        // Vérifier si l'utilisateur est autorisé
        $this->authorize('update', $ad);
        
        $request->validate([
            'photos' => 'required',
            'photos.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);
        
        $photos = [];
        
        foreach ($request->file('photos') as $photo) {
            $path = $photo->store('ads', 'public');
            $photos[] = $ad->photos()->create(['path' => $path]);
        }
        
        return response()->json($photos, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Photo $photo)
    {
        return response()->json($photo);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo)
    {
        // Vérifier si l'utilisateur est autorisé
        $this->authorize('delete', $photo);
        
        // Supprimer le fichier du stockage
        Storage::disk('public')->delete($photo->path);
        
        $photo->delete();
        
        return response()->json(null, 204);
    }
}