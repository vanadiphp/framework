<?php

namespace Vanadi\Framework\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use BezhanSalleh\FilamentShield\Traits\HasPanelShield;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Sanctum\HasApiTokens;
use LdapRecord\Laravel\Auth\AuthenticatesWithLdap;
use LdapRecord\Laravel\Auth\LdapAuthenticatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\Permission\Traits\HasRoles;
use Vanadi\Framework\Concerns\Model\HasAuditColumns;
use Vanadi\Framework\Concerns\Model\HasCode;
use Vanadi\Framework\Concerns\Model\HasState;
use Vanadi\Framework\Concerns\Model\HasTeam;

class User extends Authenticatable implements FilamentUser, HasAvatar, HasMedia, LdapAuthenticatable
{
    use AuthenticatesWithLdap;
    use HasApiTokens;
    use HasAuditColumns;
    use HasCode;
    use HasPanelShield;
    use HasRoles;
    use HasState;
    use HasTeam;
    use InteractsWithMedia;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function shouldOmitPrefix(): bool
    {
        return false;
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
            ->useFallbackUrl($this->getFilamentDefaultAvatar());
    }

    private function getFilamentDefaultAvatar()
    {
        $avatarProvider = Filament::getDefaultAvatarProvider();

        return app($avatarProvider)->get($this) ?? '';

    }

    public function getAvatarUrlAttribute(): ?string
    {
        return $this->getFilamentAvatarUrl();
    }

    public function getFilamentAvatarUrl(): ?string
    {
        $collection = 'avatars';

        return $this->getMedia($collection)->last()?->getUrl() ?? $this->getFallbackMediaUrl($collection);
    }

    public function guardName(): string
    {
        return 'web';
    }

    protected static function booted(): void
    {
        self::saving(function ($model) {
            if ($model->uac == 514) {
                $model->is_active = false;
            }
        });
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        return $this->teams->contains($tenant) || $this->hasRole(Utils::getSuperAdminName());
    }

    public function getTenants(Panel $panel): array | Collection
    {
        return $this->teams;
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_short_name', 'short_name');
    }
}
