<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengawasanIzin extends Model
{
    use HasFactory;

    protected $table = 'pengawasan_izin';
    protected $primaryKey = 'pengawasan_izin_id';
    protected $fillable = [
        'pengawasan_izin_id',
        'nama_perusahaan',
        'tgl_pengawasan',
        'hasil_pengawasan',
    ];

    protected $appends = array('id', 'tahun', 'value');

    public function getIdAttribute()
    {
        return $this->pengawasan_izin_id;
    }

    public function getValueAttribute()
    {
        return $this->nama_perusahaan;
    }

    public function getTahunAttribute()
    {
        return date("Y", strtotime($this->tgl_pengawasan));
    }
}
