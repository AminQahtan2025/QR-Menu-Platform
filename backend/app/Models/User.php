<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // تأكد من وجود حقل role في قاعدة البيانات
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // صلاحيات المستخدم
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isVendor()
    {
        return $this->role === 'vendor';
    }

    // 🌟 أضف العلاقة مع المطعم
    public function restaurant()
    {
        return $this->hasOne(Restaurant::class); // أو hasMany حسب هيكل قاعدة البيانات
    }
}
