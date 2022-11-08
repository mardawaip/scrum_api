<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kelurahan extends Model
{
    use HasFactory;

    protected $table = 'kelurahan';
    protected $primaryKey = 'kelurahan_id';
    protected $fillable = [
        'kelurahan_id',
        'kecamatan_id',
        'kelurahan_nama',
    ];

    protected $appends = array('kecamatan');

    public function getKecamatanAttribute()
    {
        if ($this->kecamatan_id) {
            return $this->JoinKecamatan->kecamatan_nama;
        }

        return [];
    }

    public function JoinKecamatan()
    {
        return $this->belongsTo(Kecamatan::class,'kecamatan_id','kecamatan_id');
    }
}
