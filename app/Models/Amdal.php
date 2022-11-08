<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Amdal extends Model
{
    use HasFactory;

    protected $table = 'amdal';
    protected $keyType = 'string';
    protected $primaryKey = 'permohonan_id';
    protected $fillable = [
        'permohonan_id',
        'member_id',
        'pemohon_id',
        'perusahaan_id',
        'tgl_permohonan',
        'jenisizin_id',
        'nama_kegiatan',
        'rencana_lokasi',
        'no_reg',
        'no_izin',
        'tgl_terbit',
        'status_perizinan',
        'keterangan',
    ];
    protected $appends = array('id', 'pemohon', 'member', 'status', 'perusahaan');

    public function getIdAttribute()
    {
        return $this->permohonan_id;
    }

    public function getPemohonAttribute()
    {
        if ($this->pemohon_id) {
            return $this->JoinPemohon->nama_pemohon;
        }

        return [];
    }

    public function getMemberAttribute()
    {
        if ($this->member_id) {
            return $this->JoinMember->nama_member;
        }

        return [];
    }

    public function getPerusahaanAttribute()
    {
        if ($this->perusahaan_id) {
            return $this->JoinPerusahaan->nama_perusahaan;
        }

        return [];
    }

    public function getStatusAttribute()
    {        
        switch ((string)$this->status_perizinan) {
            case '0': $label = "Pengajuan Izin dan Verifikasi Data"; break;
            case '1': $label = "Proses Penerbitan Izin"; break;
            
            default: $label = ""; break;
        }

        return $label;
    }

    public function JoinPemohon()
    {
        return $this->belongsTo(Pemohon::class,'pemohon_id','pemohon_id');
    }

    public function JoinMember()
    {
        return $this->belongsTo(Member::class,'member_id','member_id');
    }

    public function JoinPerusahaan()
    {
        return $this->belongsTo(Perusahaan::class, 'perusahaan_id', 'perusahaan_id');
    }
}
