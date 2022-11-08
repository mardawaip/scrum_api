<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
use App\Models\ParAmbien;


class HasilujiAmbien extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'hasiluji_ambien';
    protected $primaryKey = 'hasiluji_ambien_id';
    protected $appends = array('id','parameter','satuan', 'value');

    protected $fillable = [
        'lokasiuji_ambien_id',
        'tahunuji_ambien',
        'par_ambien_id',
        'baku_mutu',
        'tandabaca',
        'hasil_uji',
        'ket_akhir',
        'hasiluji_ambien_id',
    ];

    /**
     * 
     */
    public function parAmbien()
    {
        return $this->belongsTo(ParAmbien::class,'par_ambien_id','par_ambien_id');
        // User::class, 'foreign_key', 'owner_key'
    }


        /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getParameterAttribute()
    {
        if ($this->parAmbien) {
            return $this->parAmbien->parameter;
        }

        return "-";
    }

    public function getValueAttribute()
    {
        if ($this->parAmbien) {
            return $this->parAmbien->parameter;
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
        if ($this->parAmbien) {
            return $this->parAmbien->satuan;
        }

        return "-";
    }

    public function getIdAttribute()
    {
        return $this->hasiluji_ambien_id;
    }


}
