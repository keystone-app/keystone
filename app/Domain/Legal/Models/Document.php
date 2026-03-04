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
 * @property-read \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this> $documentable
 * @property-read User|null $user
 * @property-read Lease|null $lease
 */
class Document extends Model
{
    /** @use HasFactory<\Database\Factories\DocumentFactory> */
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

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo<\Illuminate\Database\Eloquent\Model, $this>
     */
    public function documentable(): \Illuminate\Database\Eloquent\Relations\MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Lease, $this>
     */
    public function lease(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    protected static function newFactory(): \Database\Factories\DocumentFactory
    {
        return \Database\Factories\DocumentFactory::new();
    }
}
