<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
use App\Models\ParSungaiSitu;


class HasilujiSitu extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'hasiluji_situ';
    protected $primaryKey = 'hasil_uji_situ_id';
    protected $appends = array('id','parameter','satuan', 'situ_id');

    protected $fillable = [
        'lokasiuji_situ_id',
        'tahunuji_situ',
        'periode',
        'par_sungai_situ_id',
        'baku_mutu',
        'tandabaca',
        'hasil_uji',
        'ket_akhir',
        'hasil_uji_situ_id',
    ];

    /**
     * 
     */
    public function parSungaiSitu()
    {
        return $this->belongsTo(ParSungaiSitu::class,'par_sungai_situ_id','par_sungai_situ_id');
        // User::class, 'foreign_key', 'owner_key'
    }


        /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getParameterAttribute()
    {
        if ($this->parSungaiSitu) {
            return $this->parSungaiSitu->parameter;
        }

        return "-";
    }
        /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getSatuanAttribute()
    {
        if ($this->parSungaiSitu) {
            return $this->parSungaiSitu->satuan;
        }

        return "-";
    }

    public function getIdAttribute()
    {
        return $this->hasil_uji_situ_id;
    }

    public function getSituIdAttribute()
    {
        if ($this->parSungaiSitu) {
            return $this->JoinLokasiSitu->situ_id;
        }
    }

    public function JoinLokasiSitu()
    {
        return $this->belongsTo(LokasiUjiSitu::class,'lokasiuji_situ_id','lokasiuji_situ_id');
    }
}
