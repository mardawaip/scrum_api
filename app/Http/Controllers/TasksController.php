<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class TasksController extends Controller
{
    public function get(Request $request)
    {
        $db = DB::table('tasks')->get();

        return $db;
    }
}
