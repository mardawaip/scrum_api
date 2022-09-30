<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use JWTAuth;
use DB;

class AplikasiController extends Controller
{
    public function get_aplikasi(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 12;
        $aplikasi = DB::table('aplikasi')
            ->select(
                "aplikasi.*",
                "aplikasi.deskripsi AS description"
            )
            ->where('soft_delete', 0)
            ->orderBy('create_date', 'DESC')
            ->limit($limit)
            ->get();

        $i = 0;
        foreach ($aplikasi as $opt) {
            $files = DB::table('files')->where('table', 'aplikasi')->where('header_id', $opt->aplikasi_id)->orderby('date', 'ASC')->first();
            $aplikasi[$i]->description = substr(strip_tags($opt->description), 0, 100);
            $aplikasi[$i]->screenshot = $files ? $files->path : $opt->logo;
            $i++;
        }

        return [
            'rows' => $aplikasi,
            'count' => count($aplikasi)
        ];
    }

    public function get_aplikasi_detail(Request $request, $aplikasi_id)
    {
        $aplikasi = DB::table('aplikasi')->where('aplikasi_id', $aplikasi_id)->first();
        $files = DB::table('files')->where('table', 'aplikasi')->where('header_id', $aplikasi_id)->orderby('date', 'ASC')->get();

        return ['rows' => $aplikasi, 'screenshot' => $files];
    }

    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;

        $aplikasi = DB::table('aplikasi')
            ->select(
                "aplikasi.aplikasi_id AS id",
                "aplikasi.*"
            )            
            ->orderBy("aplikasi.create_date", "DESC")
            ->where('aplikasi.soft_delete', 0);

        if($cari != ""){ $aplikasi->where("aplikasi.nama", "like", "%{$cari}%"); }

        $count_all = $aplikasi->count();
        $aplikasis = $aplikasi->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($aplikasis),
            'count_all' => $count_all,
            'rows'      => $aplikasis
        ];
    }

    public function get_perdata(Request $request)
    {
        $aplikasi_id = $request->aplikasi_id;
        $peraplikasi = DB::table('aplikasi')->where('aplikasi_id', $aplikasi_id)->first();

        return (array)$peraplikasi;
    }

    public function add(Request $request)
    {
        $data = $request->all();
        $parsed = JWTAuth::getPayload()->toArray();
        $pengguna_id = $parsed['sub'];

        $uuid = (string)Str::uuid();
        $data['aplikasi_id'] = $uuid;
        $data['soft_delete'] = "0";
        $data['create_date'] = date("Y-m-d H:i:s");

        if($request->images){
            $data['images'] = $request->images;
        }

        $create = DB::table('aplikasi')->insert($data);

        $status = $create ? 'success' : 'error';

        return [ 'status' => $status, 'id' => $uuid ];
    }

    public function update(Request $request)
    {
        $data = $request->all();
        
        if($request->images){
            $data['images'] = $request->images;
        }

        $update = DB::table('aplikasi')->where('aplikasi_id', $request->aplikasi_id)->update($data);
        $status = $update ? 'success' : 'error';

        return ['status' => $status, 'id' => $request->aplikasi_id];
    }

    public function delete(Request $request)
    {
        $delete = DB::table('aplikasi')->where('aplikasi_id', $request->aplikasi_id)->update(['soft_delete' => 1]);
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

        $upload = Storage::putFileAs('aplikasiThumbnail', $request->file('image'), $name);

        $msg = $upload ? 'Success Upload File' : 'Error Upload File';
        return response(['msg' => $msg]);
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
        $destinationPath = 'storage/aplikasiConten';
        // $upload = $file->move($destinationPath,$file->getClientOriginalName());

        $upload = Storage::putFileAs('aplikasiConten', $request->file('image'), $name);

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
            "link"          => url('/')."/".$destinationPath."/".$name,
        );

        $proses['success']  = true;
        $proses['status']   = 200;

        return response()->json($proses);
    }

    // GET REF aplikasi
    public function getSelectTentanguks(Request $request)
    {
        return [
            'status_aplikasi' => $this->getStatusBerita(),
            'kategori_aplikasi' => $this->getKategoriBerita(),
            'jenis_aplikasi' => $this->getJenisBerita()
        ];
    }

    public function getStatusBerita()
    {
        $statusBerita = DB::table("ref.status_berita")->select("status_berita_id AS value", "nama AS label")->where("soft_delete", 0)->get();
        return $statusBerita;
    }

    public function getKategoriBerita()
    {
        $KategoriBerita = DB::table("ref.kategori_berita")->select("kategori_berita_id AS value", "nama AS label")->where("soft_delete", 0)->get();
        return $KategoriBerita;
    }

    public function getJenisBerita()
    {
        $KategoriBerita = DB::table("ref.jenis_berita")->select("jenis_berita_id AS value", "nama AS label")->where("soft_delete", 0)->get();
        return $KategoriBerita;
    }

    public function saveFilesGambar(Request $request)
    {
        $id = $request->aplikasi_id;
        $uuid = (string)Str::uuid();

        $data = [
            'file_id' => $uuid,
            'nama' => @$request->nama,
            'type' => @$request->type,
            'size' => @$request->size,
            'date' => date("Y-m-d H:i:s"),
            'header_id' => $id,
            'table' => 'aplikasi',
            'path' => @$request->path
        ];

        $db = DB::table('files')->insert($data);
        return $db ? 'success' : 'error';
    }

    public function getFilesperData(Request $request)
    {
        $id = $request->aplikasi_id;
        $files = DB::table('files')->where('table', 'aplikasi')->where('header_id', $id)->orderby('date', 'ASC')->get();

        return $files;
    }

    public function deleteFile(Request $request)
    {
        $file_id = $request->file_id;

        $db = DB::table('files')->where('file_id', $file_id)->delete();
        return $db ? "success" : "error";
    }
}
