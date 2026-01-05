<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MucDoSuKien extends Model
{
    use HasFactory;

    protected $table = 'muc_do_su_kien';

    protected $fillable = [
        'ma',
        'ten',
        'mo_ta',
        'uu_tien',
    ];

    protected $casts = [
        'uu_tien' => 'integer',
    ];

    /**
     * Get alert subscriptions using this severity level.
     */
    public function thietLapCanhBaos()
    {
        return $this->hasMany(ThietLapCanhBao::class, 'muc_do_toi_thieu_id');
    }

    /**
     * Scope to order by priority.
     */
    public function scopeOrderByPriority($query, $direction = 'asc')
    {
        return $query->orderBy('uu_tien', $direction);
    }
}
