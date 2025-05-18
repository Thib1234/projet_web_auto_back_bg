<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'email', 'password', 'phone', 'address',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function contacts()
    {
        return $this->hasMany(Contact::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class);
    }
    // Dans App\Models\User

    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }
}