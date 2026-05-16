<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MasterClass extends Model
{
    public const AVAILABLE_SLOTS = ['09:00', '11:00', '13:00', '15:00'];

    /**
     * @var list<string>
     */
    protected $fillable = [
        'creativity_type_id',
        'master_id',
        'title',
        'description',
        'class_date',
        'class_time',
        'max_participants',
        'price',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'class_date' => 'date',
        'price' => 'decimal:2',
    ];

    /**
     * @return BelongsTo<CreativityType, $this>
     */
    public function creativityType(): BelongsTo
    {
        return $this->belongsTo(CreativityType::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function master(): BelongsTo
    {
        return $this->belongsTo(User::class, 'master_id');
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'enrollments')->withTimestamps();
    }

    public static function normalizeSlot(string $slot): string
    {
        return strlen($slot) === 5 ? "{$slot}:00" : $slot;
    }

    public function slotsLabel(): string
    {
        $start = CarbonImmutable::createFromFormat('H:i:s', self::normalizeSlot($this->class_time));
        $end = $start->addHours(2);

        return "{$start->format('H:i')} - {$end->format('H:i')}";
    }

    public function seatsLeft(): int
    {
        $used = (int) ($this->enrollments_count ?? $this->enrollments()->count());

        return max($this->max_participants - $used, 0);
    }

    public function hasFreeSeats(): bool
    {
        return $this->seatsLeft() > 0;
    }
}
