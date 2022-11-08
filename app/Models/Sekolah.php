<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sekolah extends Model
{
    use HasFactory;

    protected $table = 'sekolah';
    protected $primaryKey = 'sekolah_id';
    protected $fillable = [
        'sekolah_id',
        'nama_sekolah',
        'tahun_penghargaan',
        'kelurahan_id',
        'lat',
        'lng',
        'icon_map',
        'keterangan',
        'foto',
        'alamat',
        'nama_penghargaan',
        'marker-color',
        'marker-symbol',
        'marker-size',
    ];

    protected $appends = array('id', 'kelurahan', 'kecamatan', 'value');

    public function getIdAttribute()
    {
        return $this->sekolah_id;
    }

    public function getValueAttribute()
    {
        return $this->nama_sekolah;
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
