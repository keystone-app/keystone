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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function document()
    {
        return $this->belongsTo(Document::class);
    }

    public function offer()
    {
        return $this->hasOne(Offer::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\VisitFactory::new();
    }
}
