<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiUjiTanah extends Model
{
    use HasFactory;

    protected $table = 'lokasiuji_tanah';
    protected $primaryKey = 'lokasiuji_tanah_id';
    protected $appends = array('id');

    public function getIdAttribute()
    {
        return $this->lokasiuji_tanah_id;
    }
    
}
