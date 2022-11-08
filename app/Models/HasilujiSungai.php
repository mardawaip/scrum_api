<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
use App\Models\ParSungaiSitu;


class HasilujiSungai extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'hasiluji_sungai';
    protected $primaryKey = 'hasil_uji_sungai_id';
    protected $appends = array('id','parameter','satuan', 'sungai_id');

    // protected $fillable = ['title','hasiuji_sungai_file','link','position','status','jenis_benner', 'updated_at'];

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


    public function getSungaiIdAttribute()
    {
        if ($this->parSungaiSitu) {
            return $this->JoinLokasiSungai->sungai_id;
        }
    }

    public function getIdAttribute()
    {
        return $this->hasil_uji_sungai_id;
    }

    public function JoinLokasiSungai()
    {
        return $this->belongsTo(LokasiUji::class,'lokasiuji_sungai_id','lokasiuji_sungai_id');
    }

}
