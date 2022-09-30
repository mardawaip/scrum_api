<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $timeline_id
 * @property string $sekolah_id
 * @property int $tahun_ajaran_id
 * @property string $instrumen_id
 * @property int $jenis_timeline_id
 * @property string $create_date
 * @property string $last_update
 * @property int $soft_delete
 * @property string $updater_id
 * @property string $last_sync
 */
class Timeline extends Model
{
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'last_update';
    /**
     * The table associated with the model.
     * 
     * @var string
     */

    protected $connection = 'sqlsrv';

    protected $table = 'timeline';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'timeline_id';

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
    protected $fillable = ['timeline_id','sekolah_id', 'tahun_ajaran_id', 'instrumen_id', 'jenis_timeline_id', 'create_date', 'last_update', 'soft_delete', 'updater_id', 'last_sync'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */
    protected $connection = 'sqlsrv';

}
