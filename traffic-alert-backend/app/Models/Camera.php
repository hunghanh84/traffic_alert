<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Camera extends Model
{
    use HasFactory;

    protected $table = 'camera';

    protected $fillable = [
        'ma_camera',
        'ten_camera',
        'duong_id',
        'khu_vuc_id',
        'stream_url',
        'lan_kiem_tra_cuoi',
        'trang_thai_ket_noi',
        'so_lan_kiem_tra',
    ];

    protected $casts = [
        'lan_kiem_tra_cuoi' => 'datetime',
    ];

    public function duong()
    {
        return $this->belongsTo(Duong::class, 'duong_id');
    }

    public function khuVuc()
    {
        return $this->belongsTo(KhuVuc::class, 'khu_vuc_id');
    }
}
