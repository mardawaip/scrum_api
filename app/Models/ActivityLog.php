<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;
    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    protected $appends = array('causer');

    
    /**
     * Prepare proper error handling for url attribute
     *
     * @return string
     */
    // public function getAvatarUrlAttribute()
    // {
    //     if ($this->info) {
    //         return asset($this->info->avatar_url);
    //     }

    //     return asset(theme()->getMediaUrlPath().'avatars/blank.png');
    // }

    /**
     * User info relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
        public function user()
        {
            return $this->belongsTo(User::class, 'causer_id');
        }
    
    /**
     * User info relation to user model
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function getCauserAttribute()
    {
        if ($this->user) {
            return "{$this->user->first_name} {$this->user->last_name}";
        }
    }
}

