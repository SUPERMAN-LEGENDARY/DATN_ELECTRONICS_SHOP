<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    // Helper scopes
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }
    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    /**
     * Admin "thứ 1" = admin có id nhỏ nhất trong toàn hệ thống (admin gốc / cấp cao nhất).
     * Admin gốc không thể bị admin khác chỉnh sửa hoặc khoá tài khoản.
     */
    public function isFirstAdmin(): bool
    {
        if ($this->role !== 'admin') {
            return false;
        }

        $firstAdminId = static::where('role', 'admin')->oldest('id')->value('id');

        return $firstAdminId !== null && $firstAdminId === $this->id;
    }

    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function wishlistProducts()
    {
        return $this->belongsToMany(Product::class, 'wishlists')->withPivot('created_at');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->latest('created_at');
    }

    public function unreadNotificationsCount(): int
    {
        return $this->notifications()->where('is_read', false)->count();
    }

    /**
     * Gửi email đặt lại mật khẩu bằng tiếng Việt.
     */
    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}
