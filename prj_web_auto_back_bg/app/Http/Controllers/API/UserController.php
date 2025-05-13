<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class UserController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Réservé aux admins
        $this->authorize('viewAny', User::class);
        
        $users = User::with('roles')->paginate(15);
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Réservé aux admins
        $this->authorize('create', User::class);
        
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'phone' => $validatedData['phone'] ?? null,
            'address' => $validatedData['address'] ?? null,
        ]);

        $user->roles()->attach($validatedData['roles']);

        return response()->json($user->load('roles'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        // Vérification des permissions
        $this->authorize('view', $user);
        
        return response()->json($user->load('roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Vérification des permissions
        $this->authorize('update', $user);
        
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'roles' => 'sometimes|array',
            'roles.*' => 'exists:roles,id',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);

        if (isset($validatedData['roles'])) {
            $user->roles()->sync($validatedData['roles']);
        }

        return response()->json($user->load('roles'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Réservé aux admins
        $this->authorize('delete', $user);
        
        $user->delete();
        
        return response()->json(null, 204);
    }
    
    /**
     * Get the authenticated user's profile.
     */
    public function profile(Request $request)
    {
        return response()->json($request->user()->load('roles'));
    }
    
    /**
     * Update the authenticated user's profile.
     */
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $validatedData = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        if (isset($validatedData['password'])) {
            $validatedData['password'] = Hash::make($validatedData['password']);
        }

        $user->update($validatedData);
        
        return response()->json($user);
    }
}