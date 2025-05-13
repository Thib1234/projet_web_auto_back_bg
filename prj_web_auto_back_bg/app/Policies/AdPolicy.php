<?php

namespace App\Policies;

use App\Models\Ad;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true; // Tout le monde peut voir les annonces
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ad $ad): bool
    {
        return true; // Tout le monde peut voir les détails d'une annonce
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true; // Tout utilisateur authentifié peut créer une annonce
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ad $ad): bool
    {
        // L'utilisateur peut mettre à jour l'annonce s'il en est le propriétaire ou s'il est administrateur
        return $user->id === $ad->user_id || $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ad $ad): bool
    {
        // L'utilisateur peut supprimer l'annonce s'il en est le propriétaire ou s'il est administrateur
        return $user->id === $ad->user_id || $user->hasRole('admin');
    }
}