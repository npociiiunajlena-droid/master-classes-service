<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * @var list<string>
     */
    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'role',
        'photo_path',
        'password',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * @return HasMany<MasterClass, $this>
     */
    public function taughtMasterClasses(): HasMany
    {
        return $this->hasMany(MasterClass::class, 'master_id');
    }

    /**
     * @return HasMany<Enrollment, $this>
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * @return BelongsToMany<MasterClass, $this>
     */
    public function enrolledMasterClasses(): BelongsToMany
    {
        return $this->belongsToMany(MasterClass::class, 'enrollments')->withTimestamps();
    }

    public function isMaster(): bool
    {
        return $this->role === 'master';
    }

    public function isVisitor(): bool
    {
        return $this->role === 'visitor';
    }
}
