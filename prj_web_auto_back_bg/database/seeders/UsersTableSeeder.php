<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run()
    {
        // Créer un admin
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        $admin->roles()->attach($adminRole);
        
        // Créer un utilisateur normal
        $user = User::create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
        
        $user->roles()->attach($userRole);
        
        // Créer quelques utilisateurs supplémentaires
        User::factory(10)->create()->each(function ($user) use ($userRole) {
            $user->roles()->attach($userRole);
        });
    }
}