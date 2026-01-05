<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrangThaiSuKien extends Model
{
    use HasFactory;

    protected $table = 'trang_thai_su_kien';

    protected $fillable = [
        'ma',
        'ten',
        'mo_ta',
    ];
}
