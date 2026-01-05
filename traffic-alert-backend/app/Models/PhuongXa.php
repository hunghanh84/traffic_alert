<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhuongXa extends Model
{
    use HasFactory;

    protected $table = 'phuong_xa';

    protected $fillable = [
        'thanh_pho_id',
        'ten',
        'ma',
    ];

    /**
     * Get the thanh_pho that owns the phuong_xa.
     */
    public function thanhPho()
    {
        return $this->belongsTo(ThanhPho::class, 'thanh_pho_id');
    }

    /**
     * Get the khu_vuc for the phuong_xa.
     */
    public function khuVuc()
    {
        return $this->hasMany(KhuVuc::class, 'phuong_id');
    }
}
