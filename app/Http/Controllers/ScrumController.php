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

        $status = ScrumStatus::orderBy('sort', 'ASC')->get();
        $tasks = TasksAplikasi::select("tasks_id", "title")->where('aplikasi_id', $scrum->aplikasi_id)->where("type", "task")->orderby('order', 'ASC')->get();

        $i=0;
        foreach ($status as $key) {
            $status[$i]->cards = ScrumTodo::where('scrum_status_id', $key->id)->where('scrum_id', $id)->get();
            $i++;
        }

        $scrum['lists'] = $status;
        $scrum['tasks'] = $tasks;

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

        $count = 0;
        $progres = 0;
        foreach ($tasks as $key) {
            if($key->type == 'section'){
                $count = 0;
                $progres = 0;

                $section = $i;
                $tasks[$section]->progres = $progres;
                $tasks[$section]->count = $count;
            }else{
                $getScrumDetail = $this->getScrumDetailCount($key->tasks_id);
                $true = $getScrumDetail->count != 0 ? ($getScrumDetail->count == $getScrumDetail->selesai ? true : false) : false;

                if($true){
                    $tasks[$section]->progres = $tasks[$section]->progres + 1;
                }

                $tasks[$i]->completed = $getScrumDetail->proses != 0 ? "progres" : ($true ? "true" : "false");
                $tasks[$section]->count = $tasks[$section]->count + 1;
                $tasks[$i]->scrum = $getScrumDetail;
            }

            $i++;
        }

        return [
            'aplikasi' => $aplikasi,
            'tasks' => $tasks
        ];
    }

    public function getScrumDetailCount($id)
    {
        $scrum = ScrumTodo::where('tasks_id', $id)->get();

        $belum = 0;
        $proses = 0;
        $selesai = 0;

        foreach ($scrum as $key) {
            switch ($key->scrum_status_id) {
                case '107c17e9-672a-4b5a-8fad-024e3ebb4392': $belum = $belum + 1; break;
                case '439b2b7d-b95a-4558-a7c7-f24514855b80': $proses = $proses + 1; break;
                case '0880f506-ddcc-479a-b034-e34cf0089b32': $selesai = $selesai + 1; break;
                
                default:
                    // code...
                    break;
            }
        }

        return (object)[
            'belum' => $belum,
            'proses' => $proses,
            'selesai' => $selesai,
            'count' => count($scrum)
        ];
    }

    public function tasks_add(Request $request)
    {
        $data = $request->all();

        $db = TasksAplikasi::where('aplikasi_id', $request->aplikasi_id)->orderby('order', 'DESC')->first();
        $last = $db ? ($db->order + 1) : 1;

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

    public function newCard(Request $request)
    {
        $uuid = Str::uuid();

        $data = $request->newData;
        $data['scrum_todo_id'] = $uuid;
        $data['scrum_id'] = $request->boardId;
        $data['scrum_status_id'] = $request->listId;

        return ScrumTodo::create($data);
    }

    public function updateCard(Request $request)
    {
        $id = $request->scrum_todo_id;

        $todo = ScrumTodo::find($id);
        $todo->title = $request->title;
        $todo->tasks_id = $request->tasks_id;
        $todo->save();

        return true;
    }

    public function reorderListCard(Request $request)
    {
        
    }

    public function reorderCard(Request $request)
    {
        $id = $request->draggableId;

        $todo = ScrumTodo::find($id);
        $todo->scrum_status_id = $request->destination['droppableId'];
        $todo->save();

        return true;
    }
}
