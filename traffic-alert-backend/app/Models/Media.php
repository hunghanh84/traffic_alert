<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'bai_dang_id',
        'loai_media',
        'duong_dan',
        'kich_thuoc_byte',
        'dinh_dang',
        'thoi_luong',
    ];

    protected $casts = [
        'kich_thuoc_byte' => 'integer',
        'thoi_luong' => 'integer',
    ];

    /**
     * Get the bai_dang that owns the media.
     */
    public function baiDang()
    {
        return $this->belongsTo(BaiDang::class, 'bai_dang_id');
    }

    /**
     * Get the full URL for the media.
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->duong_dan);
    }

    /**
     * Get the AI results for this media.
     */
    public function ketQuaAI()
    {
        return $this->hasMany(KetQuaAI::class, 'media_id');
    }
}
