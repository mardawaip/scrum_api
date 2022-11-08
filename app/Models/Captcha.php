<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\Uuids;

class Captcha extends Model
{
    use HasFactory, Uuids;
    protected $table = 'captcha';
    protected $primrayKey = 'captcha_id';
    public $incrementing = false;
    protected $keyType = 'varchar';

    protected $fillable = [
        'nama',
    ];
}