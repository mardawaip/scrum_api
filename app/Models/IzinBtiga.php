<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IzinBtiga extends Model
{
    use HasFactory;

    protected $table = 'izin_btiga';
    protected $primaryKey = 'izin_btiga_id';
    protected $fillable = [
        'izin_btiga_id',
        'jenis_kegiatan',
        'nama_kegiatan',
        'alamat',
        'kelurahan_id',
        'pimpinan',
        'kontak',
        'telepon',
        'no_izin',
        'status',
        'tgl_terbitizin',
        'masa_berlaku',
        'lat',
        'lng',
        'icon_map',
        'marker-color',
        'marker-symbol',
        'marker-size',
        'foto',
    ];

    protected $appends = array('id', 'value');

    public function getIdAttribute()
    {
        return $this->izin_btiga_id;
    }

    public function getValueAttribute()
    {
        return $this->nama_kegiatan;
    }
}
