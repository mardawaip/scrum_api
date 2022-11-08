<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiUjiCerobong extends Model
{
    use HasFactory;

    protected $table = 'lokasiuji_cerobong';
    protected $primaryKey = 'lokasiuji_cerobong_id';
    protected $appends = array('id');

    public function getIdAttribute()
    {
        return $this->lokasiuji_cerobong_id;
    }
    
}
