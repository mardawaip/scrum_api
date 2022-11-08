<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiUjiSumur extends Model
{
    use HasFactory;

    protected $table = 'lokasiuji_sumur';
    protected $primaryKey = 'lokasiuji_sumur_id';
    protected $appends = array('id');

    public function getIdAttribute()
    {
        return $this->lokasiuji_sumur_id;
    }
    
}
