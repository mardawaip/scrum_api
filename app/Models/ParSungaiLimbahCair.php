<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParSungaiLimbahCair extends Model
{
    use HasFactory;

    protected $table = 'par_limbah_cair';
    protected $primaryKey = 'par_limbah_cair_id';

    protected $fillable = [
        'par_limbah_cair_id',
        'parameter',
        'satuan',
        'jenis',
    ];
}
