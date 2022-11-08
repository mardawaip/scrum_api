<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiUjiLLimbahCair extends Model
{
    use HasFactory;

    protected $table = 'lokasiuji_limbah_cair';
    protected $primaryKey = 'lokasiuji_limbah_cair_id';
    protected $appends = array('id');

    public function getIdAttribute()
    {
        return $this->lokasiuji_limbah_cair_id;
    }
    
}
