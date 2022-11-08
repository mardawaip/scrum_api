<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiUjiSitu extends Model
{
    use HasFactory;

    protected $table = 'lokasiuji_situ';
    protected $primaryKey = 'lokasiuji_situ_id';
    protected $appends = array('id');

    public function getIdAttribute()
    {
        return $this->lokasiuji_situ_id;
    }
    
}
