<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','height','weight','preferred_size','gender','address','phone'
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sizeRecommendations()
    {
        return $this->hasMany(SizeRecommendation::class);
    }

    public function measurementHistory()
    {
        return $this->hasMany(UserMeasurementHistory::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists');
    }
    
protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
