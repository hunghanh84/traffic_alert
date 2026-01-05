<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongKeGiaoThong extends Model
{
    use HasFactory;

    protected $table = 'thong_ke_giao_thong';

    protected $fillable = [
        'duong_id',
        'khu_vuc_id',
        'loai_thoi_gian',
        'bat_dau',
        'ket_thuc',
        'so_lan_tac_duong',
        'so_lan_ngap',
        'muc_do_tac_duong_tb',
        'muc_do_ngap_tb',
        'tong_quan',
    ];

    protected $casts = [
        'bat_dau' => 'datetime',
        'ket_thuc' => 'datetime',
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
