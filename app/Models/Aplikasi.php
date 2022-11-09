<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aplikasi extends Model
{
    use HasFactory;

    protected $table = 'aplikasi';
    protected $keyType = 'string';
    protected $primaryKey = 'aplikasi_id';
    protected $fillable = [
        'aplikasi_id',
        'nama',
        'client',
        'tanggal_mulai',
        'tanggal_selesai',
        'deskripsi',
        'created_at',
        'updated_at',
        'deleted_at',
        'logo',
    ];
}
