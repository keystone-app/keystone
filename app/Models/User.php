<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function properties()
    {
        return $this->hasMany(Property::class);
    }

    public function landlordLeases()
    {
        return $this->hasMany(Lease::class, 'landlord_id');
    }

    public function tenantLeases()
    {
        return $this->hasMany(Lease::class, 'tenant_id');
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function hasIdentityDocument(): bool
    {
        return $this->documents()->where('type', 'identity_doc')->exists();
    }

    public function getIdentityDocument()
    {
        return $this->documents()->where('type', 'identity_doc')->first();
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
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
}
