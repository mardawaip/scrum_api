<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmisiKdr extends Model
{
    use HasFactory;

    protected $table = 'emisi_kdr';
    protected $primaryKey = 'emisi_kdr_id';
    protected $fillable = [
        'emisi_kdr_id',
        'tahunuji',
        'lokasiuji',
        'jum_kdr_bensin',
        'bensin_lulus',
        'bensin_non_lulus',
        'jum_kdr_solar',
        'solar_lulus',
        'solar_non_lulus',
        'keterangan',
    ];

    protected $appends = array('id', 'value');

    public function getIdAttribute()
    {
        return $this->emisi_kdr_id;
    }

    public function getValueAttribute()
    {
        return $this->lokasiuji;
    }
}
