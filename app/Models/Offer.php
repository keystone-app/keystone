<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'visit_id',
        'amount',
        'terms',
        'status',
        'compliance_status',
    ];

    public function complianceDocuments()
    {
        return $this->hasMany(Document::class, 'user_id', 'user_id')
            ->whereIn('type', ['income_proof', 'residency_proof']);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
