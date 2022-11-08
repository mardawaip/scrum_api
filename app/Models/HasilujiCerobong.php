<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
use App\Models\ParCerobong;


class HasilujiCerobong extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'hasiluji_cerobong';
    protected $primaryKey = 'id';
    protected $appends = array('parameter','satuan', 'value');

    protected $fillable = [
        'lokasiuji_cerobong_id',
        'tahunuji_cerobong',
        'par_cerobong_id',
        'baku_mutu',
        'tandabaca',
        'hasil_uji',
        'ket_akhir',
        'id',
    ];

    /**
     * 
     */
    public function parCerobong()
    {
        return $this->belongsTo(ParCerobong::class,'par_cerobong_id','par_cerobong_id');
        // User::class, 'foreign_key', 'owner_key'
    }


        /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getParameterAttribute()
    {
        if ($this->parCerobong) {
            return $this->parCerobong->parameter;
        }

        return "-";
    }

    public function getValueAttribute()
    {
        if ($this->parCerobong) {
            return $this->parCerobong->parameter;
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
        if ($this->parCerobong) {
            return $this->parCerobong->satuan;
        }

        return "-";
    }

    // public function getIdAttribute()
    // {
    //     return $this->hasil_uji_cerobong_id;
    // }


}
