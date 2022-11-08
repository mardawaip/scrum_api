<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengaduan extends Model
{
    use HasFactory;

    protected $table = 'pengaduan';
    protected $primaryKey = 'pengaduan_id';
    protected $fillable = [
        'pengaduan_id',
        'tahun',
        'mengadukan',
        'masalah',
        'proses',
    ];

    protected $appends = array('id', 'value');

    public function getIdAttribute()
    {
        return $this->pengaduan_id;
    }

    public function getValueAttribute()
    {
        return $this->mengadukan;
    }
}
