<?php

namespace App\Models;

// use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
// use Tymon\JWTAuth\Contracts\JWTSubject;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use App\Scope\SoftDeleteScope;

/**
 * @property string $pengguna_id
 * @property int $peran_id
 * @property string $sekolah_id
 * @property string $nama
 * @property string $email
 * @property string $password
 * @property string $create_date
 * @property string $last_update
 * @property int $soft_delete
 */
// class Pengguna extends Model
class Pengguna extends Authenticatable implements JWTSubject
// class Pengguna extends Authenticatable
{
    protected $connection = 'sqlsrv';
    
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

    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'last_update';

    protected $dateFormat = 'U';
    public $timestamps = false;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'pengguna';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'pengguna_id';

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
    protected $fillable = ['pengguna_id', 'peran_id', 'nama', 'email', 'password', 'create_date', 'last_update', 'soft_delete'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function peran()
    {
        return $this->belongsTo('App\peran', 'peran_id', 'peran_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function simpanJawabans()
    {
        return $this->hasMany('App\simpanJawaban', null, 'pengguna_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pengiriman()
    {
        return $this->hasMany('App\pengiriman', null, 'pengguna_id');
    }    

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logins()
    {
        return $this->hasMany('App\login', null, 'pengguna_id');
    }

    public function getDateFormat()
    {
        return 'Y-m-d H:i:s+';
    }
}
