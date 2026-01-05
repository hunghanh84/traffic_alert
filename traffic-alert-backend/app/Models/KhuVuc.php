<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KhuVuc extends Model
{
    use HasFactory;

    protected $table = 'khu_vuc';

    protected $fillable = [
        'phuong_id',
        'ten',
        'vi_do',
        'kinh_do',
    ];

    protected $casts = [
        'vi_do' => 'double',
        'kinh_do' => 'double',
    ];

    /**
     * Get the phuong_xa that owns the khu_vuc.
     */
    public function phuongXa()
    {
        return $this->belongsTo(PhuongXa::class, 'phuong_id');
    }

    /**
     * Get the duong for the khu_vuc.
     */
    public function duong()
    {
        return $this->hasMany(Duong::class, 'khu_vuc_id');
    }
}
