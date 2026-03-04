<?php

namespace App\Domain\Property\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\States\PropertyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

class Property extends Model
{
    use HasFactory, HasStates;

    protected $fillable = [
        'user_id',
        'name',
        'address',
        'price',
        'description',
        'status',
        'type',
    ];

    protected $casts = [
        'status' => PropertyStatus::class,
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
