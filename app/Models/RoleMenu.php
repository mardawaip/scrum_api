<?php

namespace App\Models;

use App\Core\Traits\SpatieLogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleMenu extends Model
{
    use HasFactory;
    use SpatieLogsActivity;

    protected $primaryKey = 'role_menu_id';
    protected $table = 'role_menu';
    public $timestamps = false;
    protected $fillable = [
        'role_menu_id',
        'menu_id',
        'role_id',
    ];
}
