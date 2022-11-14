<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TasksAplikasi extends Model
{
    use HasFactory;

    protected $table = 'tasks_aplikasi';
    protected $keyType = 'string';
    protected $primaryKey = 'tasks_id';
    protected $appends = array('id');
    protected $fillable = [
        'tasks_id',
        'type',
        'title',
        'notes',
        'completed',
        'duoDate',
        'priority',
        'tags',
        'assignedTo',
        'subTasks',
        'aplikasi_id',
        'order',
    ];

    public function getIdAttribute()
    {
        return $this->tasks_id;
    }
}
