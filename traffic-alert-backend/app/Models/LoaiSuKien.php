<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoaiSuKien extends Model
{
    use HasFactory;

    protected $table = 'loai_su_kien';

    protected $fillable = [
        'ma',
        'ten',
        'mo_ta',
    ];
}
