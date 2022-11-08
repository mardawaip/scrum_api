<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiUjiAmbien extends Model
{
    use HasFactory;

    protected $table = 'lokasiuji_ambien';
    protected $primaryKey = 'lokasiuji_ambien_id';
    protected $appends = array('id');

    public function getIdAttribute()
    {
        return $this->lokasiuji_ambien_id;
    }
    
}
