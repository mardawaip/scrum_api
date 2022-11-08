<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityLogRequest;
use App\Http\Requests\UpdateActivityLogRequest;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // $activityLogs = ActivityLog::all();
        $request->validate([
            'limit' => 'integer']);
            
        $limit = $request->limit && $request->limit<=100 ? $request->limit : 25;

        return ActivityLog::orderBy('created_at', 'DESC')->paginate($limit);

    }
}
