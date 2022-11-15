<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScrumTodo extends Model
{
    use HasFactory;

    protected $table = 'scrum_todo';
    protected $keyType = 'string';
    protected $primaryKey = 'scrum_todo_id';
    protected $fillable = [
        'scrum_todo_id',
        'scrum_id',
        'tasks_id',
        'title',
        'description',
        'scrum_status_id',
        'created_at',
        'updated_at',
    ];
}
