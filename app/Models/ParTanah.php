<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
// use App\Models\Hasiluji;


class ParTanah extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'par_tanah';
    protected $primaryKey = 'par_tanah_id';

    //  /**
    //  * Get the comments for the blog post.
    //  */
    // public function comments()
    // {
    //     return $this->hasMany(Hasiluji::class);
    // }
}