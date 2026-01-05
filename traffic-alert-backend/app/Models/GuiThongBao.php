<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GuiThongBao extends Model
{
    use HasFactory;

    protected $table = 'gui_thong_bao';

    protected $fillable = [
        'thong_bao_id',
        'nguoi_dung_id',
        'thoi_gian_gui',
        'kenh_gui',
        'trang_thai',
        'loi',
    ];

    protected $casts = [
        'thoi_gian_gui' => 'datetime',
    ];

    public function thongBao()
    {
        return $this->belongsTo(ThongBao::class, 'thong_bao_id');
    }

    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }
}
