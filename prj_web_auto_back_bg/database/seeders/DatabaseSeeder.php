<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Ad;
use App\Models\Photo;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Créer des rôles
        $adminRole = Role::create(['name' => 'admin']);
        $userRole = Role::create(['name' => 'user']);

        // Créer un administrateur
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ]);
        $admin->roles()->attach($adminRole);

        // Créer un utilisateur
        $user = User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => bcrypt('password'),
        ]);
        $user->roles()->attach($userRole);

        // Créer une annonce (en accord avec les colonnes de la table ads)
        $ad = Ad::create([
            'user_id' => $user->id,
            'brand' => 'Toyota',
            'model' => 'Corolla',
            'year' => 2018,
            'mileage' => 50000,
            'price' => 15000.00,
            'fuel_type' => 'essence',
            'transmission' => 'automatique',
            'description' => 'Voiture en excellent état, bien entretenue.',
        ]);

        // Ajouter une photo à l'annonce
        Photo::create([
            'ad_id' => $ad->id,
            'path' => 'storage/photos/toyota_corolla.jpg',
            'is_primary' => true,
        ]);
    }
}
