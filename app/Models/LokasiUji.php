<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LokasiUji extends Model
{
    use HasFactory;

    protected $table = 'lokasiuji_sungai';
    protected $primaryKey = 'lokasiuji_sungai_id';
    protected $fillable = [
        'lokasiuji_sungai_id',
        'sungai_id',
        'lokasi_uji',
        'nama_lokasi',
        'kelurahan_id',
        'lat',
        'lng',
        'icon_map',
        'marker-color',
        'marker-symbol',
        'marker-size',
        'foto',
    ];

    protected $appends = array('id');

    public function getIdAttribute()
    {
        return $this->lokasiuji_sungai_id;
    }
    
}
