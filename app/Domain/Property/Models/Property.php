<?php

namespace App\Domain\Property\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\States\PropertyStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $address
 * @property float $price
 * @property string|null $description
 * @property string $type
 * @property PropertyStatus $status
 * @property-read User $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Lease> $leases
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Offer> $offers
 */
class Property extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyFactory> */
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Lease, $this>
     */
    public function leases(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Lease::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Offer, $this>
     */
    public function offers(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Offer::class);
    }

    protected static function newFactory(): \Database\Factories\PropertyFactory
    {
        return \Database\Factories\PropertyFactory::new();
    }
}
