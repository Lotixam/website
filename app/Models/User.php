<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'avatar',
        'password',
        'partner_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted(): void
    {
        static::updating(function (User $user): void {
            if ($user->isDirty('avatar')) {
                $previous = $user->getOriginal('avatar');
                if (is_string($previous) && $previous !== '') {
                    Storage::disk('public')->delete($previous);
                }
            }
        });

        static::deleting(function (User $user): void {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
        });
    }

    public function avatarUrl(): ?string
    {
        if (! $this->avatar) {
            return null;
        }

        $filename = basename($this->avatar);
        if ($filename === '' || $filename === '.' || $filename === '..') {
            return null;
        }

        return route('storage.avatar', ['filename' => $filename]);
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatarUrl();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin') || $this->hasRole('collaborator');
    }

    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function assignedOperations(): BelongsToMany
    {
        return $this->belongsToMany(Operation::class, 'operation_user')
            ->withPivot(['role', 'assigned_at'])
            ->withTimestamps();
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function documentRequests(): HasMany
    {
        return $this->hasMany(DocumentRequest::class, 'assigned_to_user_id');
    }

    /**
     * Libellé affiché sur la vitrine (prénom + nom du profil, ou repli sur name / username / email).
     */
    public function vitrineDisplayName(): string
    {
        $this->loadMissing('profile');
        $profile = $this->profile;
        if ($profile !== null) {
            $parts = array_filter([
                $profile->first_name,
                $profile->last_name,
            ], fn (?string $v) => $v !== null && $v !== '');
            if ($parts !== []) {
                return implode(' ', $parts);
            }
        }

        foreach ([$this->name, $this->username, $this->email] as $fallback) {
            if (is_string($fallback) && $fallback !== '') {
                return $fallback;
            }
        }

        return 'Mon compte';
    }
}
