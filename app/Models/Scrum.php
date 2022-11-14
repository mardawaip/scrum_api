<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Scrum extends Model
{
    use HasFactory;
    protected $table = 'scrum';
    protected $keyType = 'string';
    protected $primaryKey = 'scrum_id';
    protected $appends = array('id', 'title', 'setting', 'deskripsi', 'members');
    protected $fillable = [
        'scrum_id',
        'aplikasi_id',
        'setting_id',
        'created_at',
        'updated_at',
    ];

    public function getIdAttribute()
    {
        return $this->scrum_id;
    }

    public function getTitleAttribute()
    {
        if ($this->aplikasi_id) {
            return $this->aplikasi->nama;
        }

        return '';
    }

    public function getDeskripsiAttribute()
    {
        if ($this->aplikasi_id) {
            return $this->aplikasi->deskripsi;
        }

        return '';
    }

    public function getSettingAttribute()
    {
        if ($this->setting_id) {
            return $this->settings->setting_id;
        }

        return '';
    }

    public function getMembersAttribute()
    {
        return [];
    }

    public function aplikasi()
    {
        return $this->belongsTo(Aplikasi::class,'aplikasi_id','aplikasi_id');
    }

    public function settings()
    {
        return $this->belongsTo(ScrumSettings::class,'setting_id','setting_id');
    }
}
