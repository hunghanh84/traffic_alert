<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuKienGiaoThong extends Model
{
    use HasFactory;

    protected $table = 'su_kien_giao_thong';

    protected $fillable = [
        'ket_qua_ai_id',
        'duong_id',
        'khu_vuc_id',
        'loai_su_kien_id',
        'muc_do_id',
        'trang_thai_id',
        'nguon',
        'bat_dau_luc',
        'ket_thuc_luc',
        'mo_ta',
    ];

    protected $casts = [
        'bat_dau_luc' => 'datetime',
        'ket_thuc_luc' => 'datetime',
    ];

    public function ketQuaAi()
    {
        return $this->belongsTo(KetQuaAI::class, 'ket_qua_ai_id');
    }

    public function duong()
    {
        return $this->belongsTo(Duong::class, 'duong_id');
    }

    public function khuVuc()
    {
        return $this->belongsTo(KhuVuc::class, 'khu_vuc_id');
    }

    public function loaiSuKien()
    {
        return $this->belongsTo(LoaiSuKien::class, 'loai_su_kien_id');
    }

    public function mucDoSuKien()
    {
        return $this->belongsTo(MucDoSuKien::class, 'muc_do_id');
    }

    public function trang_thai_su_kien()
    {
        return $this->belongsTo(TrangThaiSuKien::class, 'trang_thai_id');
    }
}
