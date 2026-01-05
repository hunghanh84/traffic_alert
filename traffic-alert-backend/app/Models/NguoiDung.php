<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class NguoiDung extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'nguoi_dung';

    protected $fillable = [
        'ten_dang_nhap',
        'mat_khau',
        'so_dien_thoai',
        'email',
        'phuong_xa_id',
        'khu_vuc_id',
        'vai_tro',
        'trang_thai',
    ];

    protected $hidden = [
        'mat_khau',
        'remember_token',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * QUAN TRỌNG: Laravel mặc định tìm cột 'password'. 
     * Chúng ta đổi sang 'mat_khau'.
     */
    public function getAuthPassword()
    {
        return $this->mat_khau;
    }

    /**
     * Tự động mã hóa mật khẩu khi lưu vào DB
     */
    public function setMatKhauAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['mat_khau'] = bcrypt($value);
        }
    }

    /**
     * Relationships
     */
    public function phuongXa()
    {
        return $this->belongsTo(PhuongXa::class, 'phuong_xa_id');
    }

    public function khuVuc()
    {
        return $this->belongsTo(KhuVuc::class, 'khu_vuc_id');
    }

    public function baiDang()
    {
        return $this->hasMany(BaiDang::class, 'nguoi_dung_id');
    }

    /**
     * Helper methods
     */
    public function isAdmin()
    {
        return $this->vai_tro === 'admin';
    }

    public function isActive()
    {
        return $this->trang_thai === 'hoat_dong';
    }
}
