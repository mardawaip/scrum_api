<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Scope\SoftDeleteScope;

class Unduhan extends Model
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
    protected $table = 'MG_PMP1001.adm.unduhan';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'unduhan_id';

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
    protected $fillable = ['unduhan_id','judul_berita','konten_berita','kategori_berita_id','pengguna_id','status_berita_id','soft_delete','create_date','last_update','tanggal_publis','jenis_berita_id'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
}
