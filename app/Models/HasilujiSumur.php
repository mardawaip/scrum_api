<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
use App\Models\ParSumur;


class HasilujiSumur extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'hasiluji_sumur';
    protected $primaryKey = 'id';
    protected $appends = array('tanda_baca','parameter','satuan', 'value');

    // protected $fillable = ['title','hasiuji_sumur_file','link','position','status','jenis_benner', 'updated_at'];

    /**
     * 
     */
    public function parSumur()
    {
        return $this->belongsTo(ParSumur::class,'par_sumur_id','par_sumur_id');
        // User::class, 'foreign_key', 'owner_key'
    }


        /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    public function getParameterAttribute()
    {
        if ($this->parSumur) {
            return $this->parSumur->parameter;
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
        if ($this->parSumur) {
            return $this->parSumur->satuan;
        }

        return "-";
    }

    public function getValueAttribute()
    {
        if ($this->parSumur) {
            return $this->parSumur->parameter;
        }

        return "-";
    }

    // public function getIdAttribute()
    // {
    //     return $this->id;
    // }

    public function getTandaBacaAttribute()
    {
        return null;
    }


}
