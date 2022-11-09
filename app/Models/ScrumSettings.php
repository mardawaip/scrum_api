<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrumSettings extends Model
{
    use HasFactory;
    protected $table = 'scrum_settings';
    protected $keyType = 'string';
    protected $primaryKey = 'setting_id';
    protected $fillable = [
        'setting_id',
        'aplikasi_id',
        'subscribed',
    ];
}
