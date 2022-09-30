<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property string $notifikasi_id
 * @property int $jenis_notifikasi_id
 * @property string $sekolah_id
 * @property string $pengguna_id
 * @property string $keterangan
 * @property string $status
 * @property string $create_date
 * @property string $last_update
 * @property int $soft_delete
 * @property string $updater_id
 * @property string $last_sync
 * @property string $tautan
 * @property int $tahun
 * @property int $tujuan_notifikasi_id
 */
class Notifikasi extends Model
{
    protected $connection = 'sqlsrv';
    const CREATED_AT = 'create_date';
    const UPDATED_AT = 'last_update';
    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'notifikasi';

    /**
     * The primary key for the model.
     * 
     * @var string
     */
    protected $primaryKey = 'notifikasi_id';

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
    protected $fillable = ['notifikasi_id','jenis_notifikasi_id', 'sekolah_id', 'pengguna_id', 'keterangan', 'status', 'create_date', 'last_update', 'soft_delete', 'updater_id', 'last_sync', 'tautan', 'tahun', 'tujuan_notifikasi_id'];

    /**
     * The connection name for the model.
     * 
     * @var string
     */

}
