<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

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

    // Warehouse access methods
    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'user_warehouses')
            ->withTimestamps();
    }


    public function getAccessibleWarehouses()
    {
        // Admin users can access all warehouses
        if ($this->hasRole('admin')) {
            return Warehouse::pluck('id')->toArray();
        }

        // Return warehouses the user has access to
        return $this->warehouses()->pluck('warehouse_id')->toArray();
    }

    public function hasRole($role): bool
    {
        return $this->role === $role;
    }

    public function hasWarehouseAccess($warehouseId): bool
    {
//        // Admin users have access to all warehouses
//        if ($this->hasRole('admin')) {
//            return true;
//        }
//
//        // Check if user has direct access to the warehouse
//        return $this->warehouses()->where('warehouse_id', $warehouseId)->exists();

        //minimize the above code
        return in_array($warehouseId, $this->getAccessibleWarehouses());
    }


    public function giveWarehouseAccess(int $warehouseId): void
    {
        if (!$this->hasWarehouseAccess($warehouseId)) {
            $this->warehouses()->attach($warehouseId);
        }
    }

    public function revokeWarehouseAccess(int $warehouseId): void
    {
        $this->warehouses()->detach($warehouseId);
    }
}
