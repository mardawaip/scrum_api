<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Scope\SoftDeleteScope;

class FAQ extends Model
// class FAQ extends Authenticatable implements JWTSubject
{
	public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new SoftDeleteScope);
    }
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'last_update';
    protected $table = 'MG_PMP1001.adm.faq';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'faq_id';

    /**
     * The "type" of the auto-incrementing ID.
     * 
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     * 
     * @var bool
     */
    public $incrementing = false;

    /**
     * @var array
     */
    protected $fillable = ['faq_id','pertanyaan','jawaban','soft_delete','create_date','last_update'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
}
