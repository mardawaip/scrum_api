<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use JWTAuth;
use DB;

class ProdukHukumController extends Controller
{
    public function get_produk_hukum(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 9999;
        $produk_hukum = DB::table('produk_hukum')
            ->select(
                'produk_hukum.produk_hukum_id AS id',
                'produk_hukum.*',
                'jd.nama AS jenis_dokumen',
                'client.nama AS client'
            )
            ->leftJoin('ref.jenis_dokumen AS jd', 'produk_hukum.jenis_dokumen_id', '=', 'jd.jenis_dokumen_id')
            ->leftJoin('client', 'produk_hukum.client_id', '=', 'client.client_id')
            ->where('produk_hukum.soft_delete', 0)
            ->orderBy('produk_hukum.create_date', 'DESC')
            ->limit($limit)
            ->get();

            $i = 0;
            foreach ($produk_hukum as $key) {
                $files = DB::table('files')->where('table', 'produk_hukum')->where('header_id', $key->produk_hukum_id)->limit(1)->orderBy('date', 'DESC')->get();
                $produk_hukum[$i]->files = $files;
                $i++;
            }

            $select['jenis_dokumen'] = DB::table('ref.jenis_dokumen')->select("jenis_dokumen_id AS value", "nama AS label")->orderby('sorting', 'ASC')->where('soft_delete', 0)->get();

        return [
            'rows' => $produk_hukum,
            'count' => count($produk_hukum),
            'select' => $select
        ];
    }

    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;

        $produk_hukum = DB::table('produk_hukum')
            ->select(
                'produk_hukum.produk_hukum_id AS id',
                'produk_hukum.*',
                'jd.nama AS jenis_dokumen',
                'client.nama AS client'
            )
            ->leftJoin('ref.jenis_dokumen AS jd', 'produk_hukum.jenis_dokumen_id', '=', 'jd.jenis_dokumen_id')
            ->leftJoin('client', 'produk_hukum.client_id', '=', 'client.client_id')
            ->where('produk_hukum.soft_delete', 0)
            ->orderby('produk_hukum.create_date', 'DESC');

        if($cari != ""){ $produk_hukum->where("produk_hukum.nama", "like", "%{$cari}%"); }

        $count_all = $produk_hukum->count();
        $produk_hukums = $produk_hukum->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($produk_hukums),
            'count_all' => $count_all,
            'rows'      => $produk_hukums
        ];
    }

    public function add(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();
        unset($data['files']);

        $data['produk_hukum_id'] = $uuid;
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['last_update'] = date("Y-m-d H:i:s");
        $data['soft_delete'] = 0;

        if($request->logo){
            $data['logo'] = $request->logo;
        }

        $produk_hukum = DB::table('produk_hukum')->insert($data);

        $status = $produk_hukum ? "success" : "error";
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

        $produk_hukum = DB::table('produk_hukum')->where('produk_hukum_id', $request->produk_hukum_id)->update($data);
        $status = $produk_hukum ? "success" : "error";
        return ['status' => $status, 'id' => $request->produk_hukum_id];
    }

    public function delete(Request $request)
    {
        $produk_hukum = DB::table('produk_hukum')->where('produk_hukum_id', $request->produk_hukum_id)->update(['soft_delete' => 1]);
        return $produk_hukum ? "success" : "error";
    }

    public function getperprodukhukum(Request $request)
    {
        $produk_hukum = DB::table('produk_hukum')->where('produk_hukum_id', $request->produk_hukum_id)->first();
        $id = $produk_hukum->produk_hukum_id;

        $files = DB::table('files')->where('table', 'produk_hukum')->where('header_id', $id)->get();
        $produk_hukum->files = $files;

        return (array)$produk_hukum;
    }

    public function getfilesperprodukhukum(Request $request)
    {
        $id = $request->produk_hukum_id;
        $files = DB::table('files')->where('table', 'produk_hukum')->where('header_id', $id)->get();

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

    public function getSelectProdukHukum($value='')
    {
        return [
            'client' => $this->getClient(),
            'jenis_dokumen' => $this->getJenisDokumen()
        ];
    }

    public function getClient()
    {
        $client = DB::table('client')->select('client_id', 'nama')->whereIn('soft_delete', [0, 2])->get();
        return $client;
    }

    public function getJenisDokumen()
    {
        $jenis_dokumen = DB::table('ref.jenis_dokumen')->select("jenis_dokumen_id", "nama")->where('soft_delete', 0)->get();
        return $jenis_dokumen;
    }

    public function upload_files(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();

        $file = $data['files'];
        $produk_hukum_id = $request->produk_hukum_id;

        if(!$file){
            return $request->all();
        }

        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();
        $size = $file->getSize(); //size

        $format = ['JPEG', 'JPG', 'PNG', 'SVG','jpeg', 'jpg', 'png', 'svg', 'pdf', 'docx', 'doc', 'docm'];
        if(!in_array($ext, $format)){ return response(['msg' => "Format tidak didukung"]); }
        
        $imageName = time() . '.' . $ext;
        $path = Storage::disk('is3_spaces')->put('manajemen_uks/files/produk_hukum', $file, [
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
            'header_id' => $produk_hukum_id,
            'table' => 'produk_hukum',
            'path' => $path
        ];

        $db = DB::table('files')->insert($data);

        return response(['msg' => $msg]);
    }

    public function cView(Request $request, $fileId)
    {
        $cView = DB::table('files')->where('file_id', $fileId)->update(['c_view' => DB::raw('c_view + 1')]);
        return $cView ? "success" : "error";
    }

    public function cUnduh(Request $request, $fileId)
    {
        $cUnduh = DB::table('files')->where('file_id', $fileId)->update(['c_unduh' => DB::raw('c_unduh + 1')]);
        return $cUnduh ? "success" : "error";
    }

}
