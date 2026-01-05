<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThongBao extends Model
{
    use HasFactory;

    protected $table = 'thong_bao';

    protected $fillable = [
        'su_kien_id',
        'tieu_de',
        'noi_dung',
        'muc_do_uu_tien',
        'trang_thai_gui',
        'loai_thong_bao',
        'tao_boi',
    ];

    public function suKien()
    {
        return $this->belongsTo(SuKienGiaoThong::class, 'su_kien_id');
    }

    public function guiThongBaos()
    {
        return $this->hasMany(GuiThongBao::class, 'thong_bao_id');
    }
}
