<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
use App\Models\ParSungaiLimbahCair;


class HasilujiLimbahCair extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'hasiluji_limbah_cair';
    protected $primaryKey = 'id';
    protected $appends = array('parameter','satuan');

    protected $fillable = [
        'id',
        'lokasiuji_limbah_cair_id',
        'tahunuji_limbah_cair',
        'par_limbah_cair_id',
        'baku_mutu',
        'tandabaca',
        'hasil_uji',
        'ket_akhir',
    ];

    /**
     * 
     */
    public function parSungaiLimbahCair()
    {
        return $this->belongsTo(ParSungaiLimbahCair::class,'par_limbah_cair_id','par_limbah_cair_id');
        // User::class, 'foreign_key', 'owner_key'
    }


        /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getParameterAttribute()
    {
        if ($this->parSungaiLimbahCair) {
            return $this->parSungaiLimbahCair->parameter;
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
        if ($this->parSungaiLimbahCair) {
            return $this->parSungaiLimbahCair->satuan;
        }

        return "-";
    }

}
