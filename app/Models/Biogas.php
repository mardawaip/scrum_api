<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Biogas extends Model
{
    use HasFactory;

    protected $table = 'biogas';
    protected $primaryKey = 'biogas_id';
    protected $fillable = [
        'biogas_id',
        'tahun_pembuatan',
        'nama_pemilik',
        'umur',
        'kampung',
        'kelurahan_id',
        'foto',
        'lat',
        'lng',
        'icon_map',
        'sumber_energi',
        'marker-color',
        'marker-symbol',
        'marker-size',
    ];

    protected $appends = array('id', 'kelurahan', 'kecamatan', 'value');

    public function getIdAttribute()
    {
        return $this->biogas_id;
    }

    public function getValueAttribute()
    {
        return $this->nama_pemilik;
    }

    public function getKelurahanAttribute()
    {
        if ($this->kelurahan_id) {
            return $this->JoinKelurahan->kelurahan_nama;
        }

        return [];
    }

    public function getKecamatanAttribute()
    {
        if ($this->kelurahan_id) {
            return $this->JoinKelurahan->kecamatan;
        }

        return [];
    }

    public function JoinKelurahan()
    {
        return $this->belongsTo(Kelurahan::class,'kelurahan_id','kelurahan_id');
    }
}
