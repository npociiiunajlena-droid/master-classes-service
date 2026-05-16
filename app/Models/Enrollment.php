<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'master_class_id',
        'user_id',
    ];

    /**
     * @return BelongsTo<MasterClass, $this>
     */
    public function masterClass(): BelongsTo
    {
        return $this->belongsTo(MasterClass::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
