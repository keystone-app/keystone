<?php

namespace App\Domain\Legal\Models;

use App\Domain\Identity\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int|null $lease_id
 * @property int|null $user_id
 * @property string|null $documentable_type
 * @property int|null $documentable_id
 * @property string $name
 * @property string $path
 * @property string $type
 * @property-read Model|\Illuminate\Database\Eloquent\Relations\MorphTo|null $documentable
 * @property-read User|null $user
 * @property-read Lease|null $lease
 */
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

    public function documentable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function lease(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    protected static function newFactory(): \Database\Factories\DocumentFactory
    {
        return \Database\Factories\DocumentFactory::new();
    }
}
