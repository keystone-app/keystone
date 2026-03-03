<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'name',
        'path',
        'type',
    ];

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }
}
