<?php

namespace App\Domain\Legal\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Property\Models\Property;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'landlord_id',
        'tenant_id',
        'start_date',
        'end_date',
        'rent_amount',
        'status',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'tenant_id');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\LeaseFactory::new();
    }
}
