<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class KebijakanController extends Controller
{
    public function get_kebijakan(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 9999;
        $kebijakan = DB::table('kebijakan')
            ->select(
                'kebijakan.kebijakan_id AS id',
                'kebijakan.*',
                'jd.nama AS jenis_dokumen'
            )
            ->leftJoin('ref.jenis_dokumen AS jd', 'kebijakan.jenis_dokumen_id', '=', 'jd.jenis_dokumen_id')
            ->where('kebijakan.soft_delete', 0)
            ->orderBy('kebijakan.create_date', 'DESC')
            ->limit($limit)
            ->get();

            $i = 0;
            foreach ($kebijakan as $key) {
                $files = DB::table('files')->where('table', 'kebijakan')->where('header_id', $key->kebijakan_id)->limit(1)->orderBy('date', 'DESC')->get();
                $kebijakan[$i]->files = $files;
                $i++;
            }

            $select['jenis_dokumen'] = DB::table('ref.jenis_dokumen')->select("jenis_dokumen_id AS value", "nama AS label")->orderby('sorting', 'ASC')->where('soft_delete', 0)->get();

        return [
            'rows' => $kebijakan,
            'count' => count($kebijakan),
            'select' => $select
        ];
    }

    public function get(Request $request)
    {
        $kebijakan = DB::table('kebijakan')
            ->select(
                'kebijakan.kebijakan_id AS id',
                'kebijakan.*',
                'jd.nama AS jenis_dokumen'
            )
            ->leftJoin('ref.jenis_dokumen AS jd', 'kebijakan.jenis_dokumen_id', '=', 'jd.jenis_dokumen_id')
            ->where('kebijakan.soft_delete', 0)
            ->orderby('kebijakan.create_date', 'DESC');

        if($request->cari){
            $kebijakan->where('kebijakan.nama', $request->cari);
        }

        $kebijakan = $kebijakan->get();

        return ['rows' => $kebijakan, 'count' => count($kebijakan)];
    }

    public function add(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();
        unset($data['files']);

        $data['kebijakan_id'] = $uuid;
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['last_update'] = date("Y-m-d H:i:s");
        $data['soft_delete'] = 0;

        if($request->logo){
            $data['logo'] = $request->logo;
        }

        $kebijakan = DB::table('kebijakan')->insert($data);

        $status = $kebijakan ? "success" : "error";
        return ['status' => $status, 'id' => $uuid];
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

        $kebijakan = DB::table('kebijakan')->where('kebijakan_id', $request->kebijakan_id)->update($data);
        $status = $kebijakan ? "success" : "error";
        return ['status' => $status, 'id' => $request->kebijakan_id];
    }

    public function delete(Request $request)
    {
        $kebijakan = DB::table('kebijakan')->where('kebijakan_id', $request->kebijakan_id)->update(['soft_delete' => 1]);
        return $kebijakan ? "success" : "error";
    }

    public function getperkebijakan(Request $request)
    {
        $kebijakan = DB::table('kebijakan')->where('kebijakan_id', $request->kebijakan_id)->first();

        return (array)$kebijakan;
    }

    public function getSelect($value='')
    {
        return [
            'jenis_dokumen' => DB::table('ref.jenis_dokumen')->where('soft_delete', 0)->get(),
            'client' => []
        ];
    }

    public function upload_images(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();

        $file = $data['files'];
        $kebijakan_id = $request->kebijakan_id;

        if(!$file){
            return $request->all();
        }

        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();
        $size = $file->getSize(); //size

        $format = ['JPEG', 'JPG', 'PNG', 'SVG','jpeg', 'jpg', 'png', 'svg', 'pdf', 'docx', 'doc', 'docm'];
        if(!in_array($ext, $format)){ return response(['msg' => "Format tidak didukung"]); }
        
        $imageName = time() . '.' . $ext;
        $path = Storage::disk('is3_spaces')->put('manajemen_uks/files/kebijakan', $file, [
            'visibility' => 'public',
            'mimetype' => $file->getClientMimeType(),
        ]);
        // $path = Storage::putFileAs('files', $request->file('files'), strtoupper($uuid).".".$ext);

        $msg = $path ? 'Success Upload File' : 'Error Upload File';

        $data = [
            'file_id' => $uuid,
            'nama' => $name,
            'type' => $ext,
            'size' => $size,
            'date' => date("Y-m-d H:i:s"),
            'header_id' => $kebijakan_id,
            'table' => 'kebijakan',
            'path' => $path
        ];

        $db = DB::table('files')->insert($data);

        return response(['msg' => $msg]);
    }

    public function getfilesperdata(Request $request)
    {
        $id = $request->kebijakan_id;
        $files = DB::table('files')->where('table', 'kebijakan')->where('header_id', $id)->get();

        return $files;
    }

    public function deleteFile(Request $request)
    {
        $file_id = $request->file_id;
        $type = $request->type;
        $name = $file_id.".".$type;

        Storage::delete('files/'.$name);
        $db = DB::table('files')->where('file_id', $file_id)->delete();
        return $db ? "success" : "error";
    }
}
