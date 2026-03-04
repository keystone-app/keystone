<?php

namespace App\Domain\Negotiation\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Negotiation\States\OfferStatus;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\Models\Visit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

/**
 * @property int $id
 * @property int $user_id
 * @property int $property_id
 * @property int|null $visit_id
 * @property float $amount
 * @property array|string|null $terms
 * @property OfferStatus $status
 * @property string|null $compliance_status
 * @property-read User $user
 * @property-read Property $property
 * @property-read Visit|null $visit
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Document> $complianceDocuments
 */
class Offer extends Model
{
    use HasFactory, HasStates;

    protected $fillable = [
        'user_id',
        'property_id',
        'visit_id',
        'amount',
        'terms',
        'status',
        'compliance_status',
    ];

    protected $casts = [
        'status' => OfferStatus::class,
    ];

    public function complianceDocuments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Document::class, 'user_id', 'user_id')
            ->whereIn('type', ['income_proof', 'residency_proof']);
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function visit(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Visit::class);
    }

    protected static function newFactory(): \Database\Factories\OfferFactory
    {
        return \Database\Factories\OfferFactory::new();
    }
}
