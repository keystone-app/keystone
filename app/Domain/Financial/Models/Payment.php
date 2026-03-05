<?php

namespace App\Domain\Financial\Models;

use App\Domain\Financial\States\PaymentStatus;
use App\Domain\Legal\Models\Lease;
use Illuminate\Database\Eloquent\Model;
use Spatie\ModelStates\HasStates;

/**
 * @property int $id
 * @property int $lease_id
 * @property float $amount
 * @property string $type
 * @property PaymentStatus $status
 * @property-read Lease $lease
 */
class Payment extends Model
{
    use HasStates;

    protected $fillable = [
        'lease_id',
        'amount',
        'type',
        'status',
    ];

    protected $casts = [
        'status' => PaymentStatus::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<Lease, $this>
     */
    public function lease(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Lease::class);
    }
}
