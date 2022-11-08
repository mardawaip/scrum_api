<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;

class Album extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    use SoftDeletes;

    protected $table = 'album';
    protected $primaryKey = 'album_id';
    protected $fillable = [ 'album_title', 'parent' ];
}
