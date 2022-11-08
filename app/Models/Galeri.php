<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;

class Galeri extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    use SoftDeletes;

    protected $table = 'galeri';
    protected $primaryKey = 'galeri_id';
    protected $fillable = [ 'album_id', 'galeri_title', 'tipe', 'image', 'description', 'upload_date' ];
}
