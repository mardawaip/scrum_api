<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adipura extends Model
{
    use HasFactory;

    protected $table = 'adipura';
    protected $primaryKey = 'adipura_id';
    protected $fillable = [
        'adipura_id',
        'komponen',
        'nama_lokasi',
        'kelurahan_id',
        'alamat',
        'lat',
        'lng',
        'tahun_pengamatan',
        'foto',
        'icon_map',
        'marker-color',
        'marker-symbol',
        'marker-size',
    ];
}
