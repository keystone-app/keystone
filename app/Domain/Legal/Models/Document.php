<?php

namespace App\Domain\Legal\Models;

use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'lease_id',
        'user_id',
        'documentable_type',
        'documentable_id',
        'name',
        'path',
        'type',
    ];

    public function documentable()
    {
        return $this->morphTo();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lease()
    {
        return $this->belongsTo(Lease::class);
    }

    protected static function newFactory()
    {
        return \Database\Factories\DocumentFactory::new();
    }
}
