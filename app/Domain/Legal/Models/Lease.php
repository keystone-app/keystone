<?php

namespace App\Domain\Legal\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\States\LeaseStatus;
use App\Domain\Property\Models\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

/**
 * @property int $id
 * @property int $property_id
 * @property int $landlord_id
 * @property int $tenant_id
 * @property \Illuminate\Support\Carbon $start_date
 * @property \Illuminate\Support\Carbon $end_date
 * @property float $rent_amount
 * @property LeaseStatus $status
 * @property-read Property $property
 * @property-read User $landlord
 * @property-read User $tenant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Document> $documents
 */
class Lease extends Model
{
    /** @use HasFactory<\Database\Factories\LeaseFactory> */
    use HasFactory, HasStates;

    protected $fillable = [
        'property_id',
        'landlord_id',
        'tenant_id',
        'start_date',
        'end_date',
        'rent_amount',
        'status',
    ];

    protected $casts = [
        'status' => LeaseStatus::class,
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Property, $this>
     */
    public function property(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function landlord(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function tenant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany<Document, $this>
     */
    public function documents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Document::class);
    }

    protected static function newFactory(): \Database\Factories\LeaseFactory
    {
        return \Database\Factories\LeaseFactory::new();
    }
}
