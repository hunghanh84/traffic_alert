<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaiDang extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'bai_dang';

    protected $fillable = [
        'nguoi_dung_id',
        'khu_vuc_id',
        'phuong_xa_id',
        'duong_id',
        'loai_canh_bao',
        'muc_do_id',
        'mo_ta',
        'trang_thai',
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Automatically set khu_vuc_id and phuong_xa_id from duong when creating
        static::creating(function ($baiDang) {
            if ($baiDang->duong_id) {
                $duong = \App\Models\Duong::find($baiDang->duong_id);
                if ($duong) {
                    if (!$baiDang->khu_vuc_id) {
                        $baiDang->khu_vuc_id = $duong->khu_vuc_id;
                    }
                    if (!$baiDang->phuong_xa_id) {
                        $baiDang->phuong_xa_id = $duong->phuong_id;
                    }
                }
            }
        });

        // Automatically update khu_vuc_id and phuong_xa_id from duong when updating
        static::updating(function ($baiDang) {
            if ($baiDang->isDirty('duong_id')) {
                $duong = \App\Models\Duong::find($baiDang->duong_id);
                if ($duong) {
                    $baiDang->khu_vuc_id = $duong->khu_vuc_id;
                    $baiDang->phuong_xa_id = $duong->phuong_id;
                }
            }
        });
    }

    /**
     * Get the user that owns the post.
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    /**
     * Get the khu_vuc.
     */
    public function khuVuc()
    {
        return $this->belongsTo(KhuVuc::class, 'khu_vuc_id');
    }

    /**
     * Get the duong.
     */
    public function duong()
    {
        return $this->belongsTo(Duong::class, 'duong_id');
    }

    /**
     * Get the phuong_xa.
     */
    public function phuongXa()
    {
        return $this->belongsTo(PhuongXa::class, 'phuong_xa_id');
    }

    /**
     * Get the severity level (muc_do_su_kien).
     */
    public function mucDoSuKien()
    {
        return $this->belongsTo(MucDoSuKien::class, 'muc_do_id');
    }

    /**
     * Get the media for the post.
     */
    public function media()
    {
        return $this->hasMany(Media::class, 'bai_dang_id');
    }

    /**
     * Scope a query to only include pending posts.
     */
    public function scopeChoDuyet($query)
    {
        return $query->where('trang_thai', 'cho_duyet');
    }

    /**
     * Scope a query to only include approved posts.
     */
    public function scopeDaDuyet($query)
    {
        return $query->where('trang_thai', 'da_duyet');
    }

    /**
     * Scope a query to only include rejected posts.
     */
    public function scopeTuChoi($query)
    {
        return $query->where('trang_thai', 'tu_choi');
    }
    /**
     * Get the active alert settings associated with this post.
     */
    public function thietLapCanhBaos()
    {
        return $this->hasMany(ThietLapCanhBao::class, 'bai_dang_id');
    }
}
