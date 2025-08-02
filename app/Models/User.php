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
        'email',
        'password',
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

    public function ownedWarehouses()
    {
        return $this->hasMany(Warehouse::class, 'owner_id', 'id');
    }

    public function getAccessibleWarehouses()
    {
        return $this->ownedWarehouses()->get();
    }

    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function hasWarehouseAccess($warehouseId): bool
    {
        return in_array($warehouseId, $this->getAccessibleWarehouses()->pluck('id')->toArray());
    }
}
