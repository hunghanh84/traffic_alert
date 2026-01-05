<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Duong extends Model
{
    use HasFactory;

    protected $table = 'duong';

    protected $fillable = [
        'phuong_id',
        'khu_vuc_id',
        'ten',
        'ma',
        'loai_duong',
        'coordinates',
        'kich_hoat',
    ];

    protected $casts = [
        'kich_hoat' => 'boolean',
        'coordinates' => 'array',
    ];

    /**
     * Get the phuong_xa that owns the duong.
     */
    public function phuongXa()
    {
        return $this->belongsTo(PhuongXa::class, 'phuong_id');
    }

    /**
     * Get the khu_vuc that owns the duong.
     */
    public function khuVuc()
    {
        return $this->belongsTo(KhuVuc::class, 'khu_vuc_id');
    }

    /**
     * Scope a query to only include active duong.
     */
    public function scopeActive($query)
    {
        return $query->where('kich_hoat', true);
    }

    /**
     * Get the posts (alerts) for the street.
     */
    public function baiDangs()
    {
        return $this->hasMany(BaiDang::class, 'duong_id');
    }
    /**
     * Get the active alert settings for the street.
     */
    public function thietLapCanhBaos()
    {
        return $this->hasMany(ThietLapCanhBao::class, 'duong_id');
    }
}
