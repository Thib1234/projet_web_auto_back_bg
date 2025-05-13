<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as AuthServiceProvider;

// Importez vos modèles
use App\Models\Ad;
use App\Models\CartItem;
use App\Models\Contact;
use App\Models\Favorite;
use App\Models\Photo;
use App\Models\Transaction;
use App\Models\User;

// Importez vos politiques
use App\Policies\AdPolicy;
use App\Policies\CartItemPolicy;
use App\Policies\ContactPolicy;
use App\Policies\FavoritePolicy;
use App\Policies\PhotoPolicy;
use App\Policies\TransactionPolicy;
use App\Policies\UserPolicy;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Enregistrez les policies
        Gate::policy(Ad::class, AdPolicy::class);
        Gate::policy(CartItem::class, CartItemPolicy::class);
        Gate::policy(Contact::class, ContactPolicy::class);
        Gate::policy(Favorite::class, FavoritePolicy::class);
        Gate::policy(Photo::class, PhotoPolicy::class);
        Gate::policy(Transaction::class, TransactionPolicy::class);
        Gate::policy(User::class, UserPolicy::class);

        // Définir des Gates personnalisées
        Gate::define('viewDashboard', function (User $user) {
            return $user->hasRole('admin');
        });

        Gate::define('exportData', function (User $user) {
            return $user->hasRole('admin');
        });
    }
}