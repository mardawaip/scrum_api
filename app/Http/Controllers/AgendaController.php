<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use JWTAuth;
use DB;

class AgendaController extends Controller
{
    public function get_agenda(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 12;
        $offset = ($page * $limit)-$limit;
        $cari = $request->cari;
        $jenis_agenda = $request->jenis_agenda;

        $agenda = DB::table('agenda')
            ->select(
                "agenda.agenda_id",
                "agenda.nama",
                "agenda.mulai",
                "agenda.selesai",
                "agenda.konten AS description",
                "agenda.file1",
                "agenda.file2",
                "jenis_agenda.nama AS jenis_agenda",
                "client.nama AS client"

            )
            ->where('agenda.soft_delete', 0)
            ->orderBy('agenda.create_date', 'DESC')
            ->leftJoin("ref.jenis_agenda", "agenda.jenis_agenda_id", "=", "jenis_agenda.jenis_agenda_id")
            ->leftJoin("client", "agenda.client_id", "=", "client.client_id");

        if($jenis_agenda != ''){
            $agenda->where('jenis_agenda.nama', $jenis_agenda);
        }

        if($cari != ''){
            $agenda->where('agenda.nama', 'like', "%$cari%");
        }

        $count_all = $agenda->count();
        $rows = $agenda->limit($limit)->offset($offset)->get();

        $i = 0;
        foreach ($rows as $opt) {
            $rows[$i]->description = substr(strip_tags($opt->description), 0, 100);
            $i++;
        }

        $jenis_agenda = DB::table('ref.jenis_agenda')->where('soft_delete', 0)->get();

        return [
            'jenis_agenda' => $jenis_agenda,
            'rows' => $rows,
            'count' => count($rows),
            'count_all' => $count_all            
        ];
    }

    public function get_agenda_detail(Request $request, $agenda_id)
    {
        $agenda = DB::table('agenda')
            ->select("agenda.*", "jenis_agenda.nama AS jenis_agenda")
            ->leftJoin("ref.jenis_agenda AS jenis_agenda", "agenda.jenis_agenda_id", "=", "jenis_agenda.jenis_agenda_id")
            ->where('agenda.agenda_id', $agenda_id)
            ->first();

        return (array)$agenda;
    }

    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;

        $agenda = DB::table('agenda')
            ->select(
                "agenda.agenda_id AS id",
                "agenda.agenda_id",
                "agenda.nama",
                "agenda.mulai",
                "agenda.selesai",
                "agenda.create_date",
                "jb.nama AS jenis_agenda",
                "cb.nama AS client"
            )
            ->leftJoin("ref.jenis_agenda AS jb", "agenda.jenis_agenda_id", "=", "jb.jenis_agenda_id")
            ->leftJoin("client AS cb", "agenda.client_id", "=", "cb.client_id")
            ->orderBy("agenda.create_date", "DESC")
            ->where('agenda.soft_delete', 0);

        if($cari != ""){ $agenda->where("agenda.nama", "like", "%{$cari}%"); }
        if($request->jenis_agenda_id != ""){ $agenda->where("agenda.jenis_agenda_id", $request->jenis_agenda_id); }

        $count_all = $agenda->count();
        $agendas = $agenda->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($agendas),
            'count_all' => $count_all,
            'rows'      => $agendas
        ];
    }

    public function get_peragenda(Request $request)
    {
        $agenda_id = $request->agenda_id;
        $peragenda = DB::table('agenda')->where('agenda_id', $agenda_id)->first();

        return (array)$peragenda;
    }

    public function add(Request $request)
    {
        $data = $request->all('jenis_agenda_id','nama','mulai','selesai','konten','client_id');
        // $parsed = JWTAuth::getPayload()->toArray();

        $uuid = (string)Str::uuid();
        $data['agenda_id'] = $uuid;
        $data['soft_delete'] = "0";
        $data['create_date'] = date("Y-m-d H:i:s");

        // if($request->images){
        //     $data['images'] = $request->images;
        // }

        $create = DB::table('agenda')->insert($data);


        return $status = $create ? 'success' : 'error';
        // return ['status' => $status, 'id' => $uuid];
    }

    public function update(Request $request)
    {
        $data = $request->all('jenis_agenda_id','nama','mulai','selesai','konten','client_id');
        // $data['slug'] = str_replace([" ", "/"], "-", $request->judul);
        
        // if($request->images){
        //     $data['images'] = $request->images;
        // }

        $update = DB::table('agenda')->where('agenda_id', $request->agenda_id)->update($data);

        return $status = $update ? 'success' : 'error';
        // return ['status' => $status, 'id' => $request->agenda_id];
    }

    public function delete(Request $request)
    {
        $delete = DB::table('agenda')->where('agenda_id', $request->agenda_id)->update(['soft_delete' => 1]);
        return $delete ? 'success' : 'error';
    }

    public function upload_images(Request $request)
    {
        $file = $request->image;
        if(!$file){
            return $request->all();
        }

        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();

        $format = ['jpeg', 'jpg', 'png', 'svg'];
        if(!in_array($ext, $format)){ return response(['msg' => "Format tidak didukung"]); }

        // $upload = Storage::putFileAs('agendaThumbnail', $request->file('image'), $name);
        $path = Storage::disk('is3_spaces')->put('manajemen_uks/files/images', $file, [
            'visibility' => 'public',
            'mimetype' => $file->getClientMimeType(),
        ]);

        $link = 'https://is3.cloudhost.id/storagedirectus1/'.$path;

        $msg = $path ? 'Success Upload File' : 'Error Upload File';
        return response(['msg' => $msg, 'link' => $link]);
    }

    public function upload(Request $request)
    {
        $data = $request->all();
        $file = $data['image'];

        $ext  = $file->getClientOriginalExtension(); //extensi
        $size = $file->getSize(); //size
        $name = $file->getClientOriginalName(); //namefile

        $format = ['jpeg', 'jpg', 'png', 'svg', 'gif'];
        if(!in_array($ext, $format)){ return response(['data' => [], 'success' => false, 'status' => 200]); }

        //Move Uploaded File
        $destinationPath = 'storage/agendaConten';
        // $upload = $file->move($destinationPath,$file->getClientOriginalName());

        // $upload = Storage::putFileAs('agendaConten', $request->file('image'), $name);
        $path = Storage::disk('is3_spaces')->put('manajemen_uks/files/images', $file, [
            'visibility' => 'public',
            'mimetype' => $file->getClientMimeType(),
        ]);

        $link = 'https://is3.cloudhost.id/storagedirectus1/'.$path;

        $proses['data'] = array(
            "id"            => "GJFuCG5",
            "title"         => null,
            "description"   => null,
            "datetime"      => time(),
            "type"          => "image/".$ext,
            "animated"      => false,
            "width"         => 1600,
            "height"        => 900,
            "size"          => $size,
            "views"         => 1,
            "bandwidth"     => 0,
            "vote"          => null,
            "favorite"      => false,
            "nsfw"          => null,
            "section"       => null,
            "account_url"   => null,
            "account_id"    => 0,
            "is_ad"         => false,
            "in_most_viral" => false,
            "has_sound"     => false,
            "tags"          => [],
            "ad_type"       => 0,
            "ad_url"        => "",
            "in_gallery"    => false,
            "deletehash"    => "67eK80Moa77ONlW",
            "name"          => "",
            "link"          => $link,
        );

        $proses['success']  = true;
        $proses['status']   = 200;

        return response()->json($proses);
    }

    // GET REF BERITA
    public function getSelectAgenda(Request $request)
    {
        return [
            'jenis_agenda' => $this->getJenisAgenda(),
            'client' => $this->getClient()
        ];
    }

    public function getJenisAgenda()
    {
        $KategoriAgenda = DB::table("ref.jenis_agenda")->select("jenis_agenda_id AS value", "nama AS label")->where("soft_delete", 0)->get();
        return $KategoriAgenda;
    }
    
    public function getClient()
    {
        $Client = DB::table("client")->select("client_id AS value", "nama AS label")->where("soft_delete", 0)->get();
        return $Client;
    }
}
