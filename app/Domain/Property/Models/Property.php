<?php

namespace App\Domain\Property\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Negotiation\Models\Offer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'description',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leases()
    {
        return $this->hasMany(Lease::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\PropertyFactory::new();
    }
}
