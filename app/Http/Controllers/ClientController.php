<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class ClientController extends Controller
{
    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;

        $name = $request->name ? explode(",", $request->name) : [];
        $notnama = $request->notnama ? explode(",", $request->notnama) : [];

        $client = DB::table('client')->select('client_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $client->where('nama', $request->cari);
        }

        if($request->name){
            $client->whereIn('nama', $name);
        }

        $count_all = $client->count();
        $client = $client->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($client),
            'count_all' => $count_all,
            'rows'      => $client
        ];
    }

    public function add(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();
        unset($data['files']);

        $data['client_id'] = $uuid;
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['last_update'] = date("Y-m-d H:i:s");
        $data['soft_delete'] = 0;

        if($request->logo){
            $data['logo'] = $request->logo;
        }

        $client = DB::table('client')->insert($data);

        return $client ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $data = $request->all();
        unset($data['files']);
        unset($data['logo']);
        $data['last_update'] = date("Y-m-d H:i:s");

        if($request->logo != null){
            $data['logo'] = $request->logo;
        }

        $client = DB::table('client')->where('client_id', $request->client_id)->update($data);
        return $client ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $client = DB::table('client')->where('client_id', $request->client_id)->update(['soft_delete' => 1]);
        return $client ? "success" : "error";
    }

    public function getperclient(Request $request)
    {
        $client = DB::table('client')->where('client_id', $request->client_id)->first();

        return (array)$client;
    }

    public function getSelectClient($value='')
    {
        return [];
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

        // $destinationPath = base_path('/public/assets/img/berita');
        // $upload = $file->move($destinationPath, $name);
        $upload = Storage::putFileAs('clientThumbnail', $request->file('image'), $name);

        $msg = $upload ? 'Success Upload File' : 'Error Upload File';
        return response(['msg' => $msg]);
    }
}
