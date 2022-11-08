<?php

namespace App\Models;

use App\Core\Traits\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    use HasFactory;
    use SpatieLogsActivity;

    protected $primaryKey = 'menu_id';
    protected $table = 'menu';
    protected $fillable = [
        'menu_id',
        'kode',
        'title',
        'type',
        'icon',
        'auth',
        'url',
        'induk_menu_id',
        'nomor_urut',
        'tingkat_menu',
        'soft_delete',
        'dashboard',
        'dashboard-icon',
        'dashboard-icon-hover',
        'type-menu',
        'status',
    ];
}
