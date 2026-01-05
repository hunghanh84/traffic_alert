<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ThietLapCanhBao extends Model
{
    use HasFactory;

    protected $table = 'thiet_lap_canh_bao';

    protected $fillable = [
        'bai_dang_id',
        'nguoi_dung_id',
        'duong_id',
        'loai_canh_bao',
        'muc_do_toi_thieu_id',
        'thoi_gian_bat_dau',
        'thoi_gian_ket_thuc',
        'thu_trong_tuan',
        'kenh_nhan',
        'kich_hoat',
        'trang_thai',
    ];

    /**
     * Get the source post (bai_dang) for this active alert.
     */
    public function baiDang()
    {
        return $this->belongsTo(BaiDang::class, 'bai_dang_id');
    }

    protected $casts = [
        'thoi_gian_bat_dau' => 'datetime',
        'thoi_gian_ket_thuc' => 'datetime',
        'thu_trong_tuan' => 'integer',
        'kich_hoat' => 'boolean',
    ];

    /**
     * Get the user who owns this alert subscription.
     */
    public function nguoiDung()
    {
        return $this->belongsTo(NguoiDung::class, 'nguoi_dung_id');
    }

    /**
     * Get the street for this subscription.
     */
    public function duong()
    {
        return $this->belongsTo(Duong::class, 'duong_id');
    }

    /**
     * Get the minimum severity level.
     */
    public function mucDoToiThieu()
    {
        return $this->belongsTo(MucDoSuKien::class, 'muc_do_toi_thieu_id');
    }

    /**
     * Scope to get only active subscriptions.
     */
    public function scopeActive($query)
    {
        return $query->where('kich_hoat', true);
    }

    /**
     * Scope to get subscriptions for a specific street.
     */
    public function scopeForStreet($query, $duongId)
    {
        return $query->where('duong_id', $duongId);
    }

    /**
     * Scope to get subscriptions for a specific user.
     */
    public function scopeForUser($query, $nguoiDungId)
    {
        return $query->where('nguoi_dung_id', $nguoiDungId);
    }

    /**
     * Check if subscription is active for current day and time.
     */
    public function isActiveNow()
    {
        if (!$this->kich_hoat) {
            return false;
        }

        $now = Carbon::now();
        $dayOfWeek = $now->dayOfWeek; // 0=Sunday, 1=Monday...
        $dayBit = 1 << $dayOfWeek;

        // Check if current day is enabled
        if (!($this->thu_trong_tuan & $dayBit)) {
            return false;
        }

        // Check time range if specified
        if ($this->thoi_gian_bat_dau && $this->thoi_gian_ket_thuc) {
            $currentTime = $now->format('H:i:s');
            $startTime = Carbon::parse($this->thoi_gian_bat_dau)->format('H:i:s');
            $endTime = Carbon::parse($this->thoi_gian_ket_thuc)->format('H:i:s');

            if ($currentTime < $startTime || $currentTime > $endTime) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if alert severity meets minimum threshold.
     */
    public function shouldNotify($alertSeverity)
    {
        $severityMap = [
            'low' => 1,
            'medium' => 2,
            'high' => 3,
            'critical' => 4,
        ];

        $alertPriority = $severityMap[$alertSeverity] ?? 0;
        $minPriority = $this->mucDoToiThieu->uu_tien ?? 0;

        return $alertPriority >= $minPriority;
    }
}
