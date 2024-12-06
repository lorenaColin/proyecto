<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
       'email',
        'password',
        'type',
        'multi_rfc',
        'status',
        'ultima_conexion',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            
        ];
    }
    public function companies()
    {
        return $this->hasMany(Company::class, 'id_usr_create');
    }
    public function getJWTIdentifier()
    {
        return $this->getKey();  
    }

    public function getJWTCustomClaims()
    {
        return [];  
    }
    public function collaboratorCompanies()
{
    return $this->belongsToMany(Company::class, 'collaborator_company', 'collaborator_id', 'company_id')
                ->withPivot('permiso') 
                ->withTimestamps();
}
}
