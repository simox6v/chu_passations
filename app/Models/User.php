<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Passation;
/**
 * @method \Illuminate\Database\Eloquent\Relations\HasMany passations()
 */


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ✅ Relation : un utilisateur peut avoir plusieurs passations
    public function passations()
    {
        return $this->hasMany(Passation::class);
    }

    public function hasRole($role)
    {
        return $this->role === $role;
    }
}
