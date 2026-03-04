<?php

namespace App\Domain\Scheduling\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Document;
use App\Domain\Negotiation\Models\Offer;
use App\Domain\Property\Models\Property;
use App\Domain\Scheduling\States\VisitStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

/**
 * @property int $id
 * @property int $user_id
 * @property int $property_id
 * @property int|null $document_id
 * @property \Illuminate\Support\Carbon $visit_at
 * @property VisitStatus $status
 * @property-read User $user
 * @property-read Property $property
 * @property-read Document|null $document
 * @property-read Offer|null $offer
 */
class Visit extends Model
{
    use HasFactory, HasStates;

    protected $fillable = [
        'user_id',
        'property_id',
        'document_id',
        'visit_at',
        'status',
    ];

    protected $casts = [
        'visit_at' => 'datetime',
        'status' => VisitStatus::class,
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function property(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function document(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function offer(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Offer::class);
    }

    protected static function newFactory(): \Database\Factories\VisitFactory
    {
        return \Database\Factories\VisitFactory::new();
    }
}
