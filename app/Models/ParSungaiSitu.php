<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;
use App\Core\Traits\SpatieLogsActivity;
use App\Models\HasilujiSungai;


class ParSungaiSitu extends Model
{
    use HasFactory;
    use SpatieLogsActivity;
    // use SoftDeletes;

    protected $table = 'par_sungai_situ';
    protected $primaryKey = 'par_sungai_situ_id';

     /**
     * Get the comments for the blog post.
     */
    public function comments()
    {
        return $this->hasMany(HasilujiSungai::class);
    }
}
