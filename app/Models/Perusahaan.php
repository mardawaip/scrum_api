<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    use HasFactory;

    protected $table = 'perusahaan';
    protected $primaryKey = 'perusahaan_id';
    protected $fillable = [
        'perusahaan_id',
        'nama_perusahaan',
        'status_perusahaan',
        'alamat_perusahaan',
        'kelurahan_id',
        'lat',
        'lng',
        'notelp_perusahaan',
        'jenis_usaha',
        'kapasitas_produksi',
        'luas_lahan',
        'luas_bangunan',
        'kondisi',
        'member_id',
    ];
}
