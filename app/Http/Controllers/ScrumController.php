<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scrum;
use App\Models\Aplikasi;
use App\Models\ScrumSettings;
use App\Models\TasksAplikasi;
use App\Models\ScrumStatus;
use App\Models\ScrumTodo;
use Illuminate\Support\Str;
use DB;

class ScrumController extends Controller
{
    public function index(Request $request)
    {
        // code...
    }

    public function getMembers(Request $request)
    {
        return [];
    }

    public function getScrum(Request $request)
    {
        return Scrum::orderBy('created_at', 'DESC')->get();
    }

    public function addScrum()
    {
        $uuid = Str::uuid();

        $dataAplikasi = [
            'aplikasi_id'   => $uuid,
            'nama'          => 'Untitled Board',
        ];

        $dataSetting = [
            'setting_id'    => $uuid,
        ];

        $dataScrum = [
            'scrum_id'      => $uuid,
            'aplikasi_id'   => $uuid,
            'setting_id'    => $uuid
        ];
        
        Aplikasi::create($dataAplikasi);
        ScrumSettings::create($dataSetting);
        return Scrum::create($dataScrum);
    }

    public function updateScrum(Request $request, $id)
    {
        $title = $request->title;

        $aplikasi = Aplikasi::find($id);
        $aplikasi->nama = $title;
        $aplikasi->save();

        return Scrum::find($id);
    }

    public function getScrumDetail(Request $request, $id)
    {
        $scrum = Scrum::find($id);

        $status = ScrumStatus::orderBy('id', 'ASC')->get();

        $i=0;
        foreach ($status as $key) {
            $status[$i]->cards = ScrumTodo::where('scrum_status_id', $key->id)->where('scrum_id', $id)->get();
            $i++;
        }

        $scrum['lists'] = $status;

        return $scrum;
    }

    public function getAplikasiDetail(Request $request, $id)
    {
        $Scrum = Scrum::find($id);

        $aplikasi_id = $Scrum->aplikasi_id;

        $aplikasi = Aplikasi::find($aplikasi_id);
        $tasks = TasksAplikasi::where('aplikasi_id', $aplikasi_id)->orderby('order', 'ASC')->get();

        $i = 0;
        $section = 0;
        $math = [];
        $true = [];
        $prev = '';
        foreach ($tasks as $key) {
            if($key->type == 'section'){
                if($prev == 'task'){
                    $count = array_sum($math);
                    $acc = array_sum($true);
                    $progres = ($acc/$count)*100;
                    $tasks[$section]->progres = $progres;
                    $math = [];
                    $true = [];
                }

                $section = $i;
                $prev = 'section';
            }else{
                $math[] = 1;
                if($key->completed == 'true'){
                    $true[] = 1;
                }
                $prev = 'task';
            }

            $i++;
        }

        return [
            'aplikasi' => $aplikasi,
            'tasks' => $tasks
        ];
    }

    public function tasks_add(Request $request)
    {
        $data = $request->all();

        $last = TasksAplikasi::orderby('order', 'DESC')->where('aplikasi_id', $request->aplikasi_id)->first()->order + 1;

        $uuid = Str::uuid();
        $data['tasks_id'] = $uuid;
        $data['order'] = $last;
        $data['completed'] = $request->completed;

        if($request->multi){
            $titles = explode("\n", $request->title);

            for ($i=0; $i < count($titles); $i++) { 
                $uuid = Str::uuid();

                $datas = $data;
                $datas['tasks_id'] = $uuid;
                $datas['title'] = $titles[$i];

                TasksAplikasi::create($datas);
            }

            return true;
        }else{
            return TasksAplikasi::create($data);
        }
    }

    public function tasks_update(Request $request, $id)
    {
        $db = TasksAplikasi::find($id);
        $db->title = $request->title;
        $db->save();

        return true;
    }

    public function tasks_delete(Request $request, $id)
    {
        $db = TasksAplikasi::where('tasks_id', $id)->delete();

        return true;
    }

    public function reorderList(Request $request)
    {
        $datas = $request->datas;

        foreach ($datas as $key) {
            $db = DB::table('tasks_aplikasi')
                ->where('tasks_id', $key['tasks_id'])
                ->update([
                    'order' => $key['order']
                ]);
        }

        return true;
    }

}
