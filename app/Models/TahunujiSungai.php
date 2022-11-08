<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;

class TahunujiSungai extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    use SoftDeletes;

    protected $table = 'tahunuji_sungai';
    protected $primaryKey = 'tahunuji_sungai';
    protected $fillable = ['tahunuji_sungai','keterangan'];
    protected $appends = array('id', 'description');

    public function getIdAttribute()
    {
        return $this->tahunuji_sungai;
    }

    public function getDescriptionAttribute()
    {
        return substr(strip_tags($this->keterangan), 0, 100);
    }
}
