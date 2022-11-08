<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemohon extends Model
{
    protected $table = 'pemohon';
    protected $keyType = 'string';
    protected $primaryKey = 'pemohon_id';
    protected $fillable = ['pemohon_id', 'nama_pemohon', 'jabatan', 'alamat_pemohon', 'kelurahan_id', 'nik_pemohon', 'notelp_pemohon', 'member_id'];
}
