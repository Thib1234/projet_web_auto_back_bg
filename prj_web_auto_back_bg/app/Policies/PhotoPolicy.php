<?php

namespace App\Policies;

use App\Models\Photo;
use App\Models\User;

class PhotoPolicy
{
    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Photo $photo): bool
    {
        // L'utilisateur peut supprimer la photo s'il est propriétaire de l'annonce ou admin
        return $user->id === $photo->ad->user_id || $user->hasRole('admin');
    }
}