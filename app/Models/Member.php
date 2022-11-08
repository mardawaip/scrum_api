<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $table = 'member';
    protected $primaryKey = 'member_id';
    protected $fillable = [
        'member_id',
        'nama_member',
        'alamat_member',
        'nik_member',
        'email',
        'password',
        'image_ktp',
        'status_member'
    ];
}
