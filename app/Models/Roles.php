<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';
    protected $table = 'roles';
    protected $fillable = [
        'id',
        'name',
        'guard_name',
    ];
}
