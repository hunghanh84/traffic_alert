<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThanhPho extends Model
{
    use HasFactory;

    protected $table = 'thanh_pho';

    protected $fillable = [
        'ten',
        'ma',
    ];

    /**
     * Get the phuong_xa for the thanh_pho.
     */
    public function phuongXa()
    {
        return $this->hasMany(PhuongXa::class, 'thanh_pho_id');
    }
}
