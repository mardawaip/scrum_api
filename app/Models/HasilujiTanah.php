<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
use App\Models\ParTanah;


class HasilujiTanah extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'hasiluji_tanah';
    protected $primaryKey = 'id';
    protected $appends = array('parameter','satuan', 'value');

    protected $fillable = [
        'lokasiuji_tanah_id',
        'tahunuji_tanah',
        'par_tanah_id',
        'baku_mutu',
        'tandabaca',
        'hasil_uji',
        'ket_akhir',
        'id',
    ];

    /**
     * 
     */
    public function parTanah()
    {
        return $this->belongsTo(ParTanah::class,'par_tanah_id','par_tanah_id');
        // User::class, 'foreign_key', 'owner_key'
    }


        /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getParameterAttribute()
    {
        if ($this->parTanah) {
            return $this->parTanah->parameter;
        }

        return "-";
    }

    public function getValueAttribute()
    {
        if ($this->parTanah) {
            return $this->parTanah->parameter;
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
        if ($this->parTanah) {
            return $this->parTanah->satuan;
        }

        return "-";
    }

    // public function getIdAttribute()
    // {
    //     return $this->hasil_uji_tanah_id;
    // }


}
