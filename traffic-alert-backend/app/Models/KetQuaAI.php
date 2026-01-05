<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KetQuaAI extends Model
{
    use HasFactory;

    protected $table = 'ket_qua_ai';

    protected $fillable = [
        'media_id',
        'nhan',
        'do_tin_cay',
        'raw_json',
        'da_xac_minh',
        'xac_minh_boi',
    ];

    protected $casts = [
        'raw_json' => 'array',
        'da_xac_minh' => 'boolean',
        'do_tin_cay' => 'double',
    ];

    /**
     * Get the media that owns the AI result.
     */
    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    /**
     * Get the user who verified this result.
     */
    public function nguoiXacMinh()
    {
        return $this->belongsTo(NguoiDung::class, 'xac_minh_boi');
    }

    /**
     * Scope to get only verified results.
     */
    public function scopeVerified($query)
    {
        return $query->where('da_xac_minh', true);
    }

    /**
     * Scope to get unverified results.
     */
    public function scopeUnverified($query)
    {
        return $query->where('da_xac_minh', false);
    }

    /**
     * Scope to get results by label.
     */
    public function scopeByLabel($query, $label)
    {
        return $query->where('nhan', $label);
    }

    /**
     * Scope to get high confidence results.
     */
    public function scopeHighConfidence($query, $threshold = 0.8)
    {
        return $query->where('do_tin_cay', '>=', $threshold);
    }
}
