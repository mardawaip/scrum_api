<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;

class Banner extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    use SoftDeletes;

    protected $table = 'banner';
    protected $primaryKey = 'banner_id';
    protected $fillable = ['title','banner_file','link','position','status','jenis_benner', 'updated_at'];
}
