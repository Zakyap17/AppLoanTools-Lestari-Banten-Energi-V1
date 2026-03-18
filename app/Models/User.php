<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'guid',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Validate the user's password (supports both local and LDAP).
     *
     * @param string $password
     * @return bool
     */
    public function validatePassword(string $password): bool
    {
        // 1. If user has guid and LDAP is enabled, try LDAP first
        if ($this->guid && config('services.ldap.enabled')) {
            $ldapService = app(\App\Services\LdapService::class);
            if ($ldapService->authenticate($this->username, $password)) {
                return true;
            }
        }

        // 2. Fallback to local hash check (for Admin/Local users or if LDAP fails/is disabled)
        return \Illuminate\Support\Facades\Hash::check($password, $this->password);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
