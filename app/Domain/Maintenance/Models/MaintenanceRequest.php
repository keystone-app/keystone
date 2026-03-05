<?php

namespace App\Domain\Maintenance\Models;

use App\Domain\Identity\Models\User;
use App\Domain\Legal\Models\Lease;
use App\Domain\Maintenance\States\MaintenanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

/**
 * @property int $id
 * @property int $lease_id
 * @property int $user_id
 * @property string $title
 * @property string|null $description
 * @property MaintenanceStatus $status
 * @property-read Lease $lease
 * @property-read User $user
 */
class MaintenanceRequest extends Model
{
    use HasStates, HasFactory;

    protected $fillable = [
        'lease_id',
        'user_id',
        'title',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => MaintenanceStatus::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Lease, $this>
     */
    public function lease(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<User, $this>
     */
    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): \Database\Factories\MaintenanceRequestFactory
    {
        return \Database\Factories\MaintenanceRequestFactory::new();
    }
}
