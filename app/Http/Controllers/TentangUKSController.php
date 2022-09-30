<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use JWTAuth;
use DB;

class TentangUKSController extends Controller
{
    public function get_tentang_uks(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 12;
        $tentang_uks = DB::table('tentang_uks')
            ->select(
                "tentang_uks.judul",
                "tentang_uks.slug",
                "tentang_uks.images",
                "tentang_uks.konten_tentang_uks AS description"
            )
            ->where('status_tentang_uks_id', 1)
            ->where('soft_delete', 0)
            ->orderBy('create_date', 'DESC')
            ->limit($limit)
            ->get();

        $i = 0;
        foreach ($tentang_uks as $opt) {
            $tentang_uks[$i]->description = substr(strip_tags($opt->description), 0, 100);
            $i++;
        }

        return [
            'rows' => $tentang_uks,
            'count' => count($tentang_uks)
        ];
    }

    public function get_tentang_uks_detail(Request $request, $slug)
    {
        $tentang_uks = DB::table('tentang_uks')->select("slug AS id", "konten_tentang_uks AS konten",  "*")->where('slug', $slug)->first();
        $label = DB::table('tentang_uks')
            ->select('slug AS value', 'judul AS label')
            ->where('soft_delete', 0)
            ->orderby('sorting', 'ASC')
            ->get();

        return [
            'rows' => $tentang_uks,
            'label' => $label
        ];
    }

    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;

        $tentang_uks = DB::table('tentang_uks')
            ->select(
                "tentang_uks.tentang_uks_id AS id",
                "tentang_uks.tentang_uks_id",
                "tentang_uks.judul",
                "tentang_uks.slug",
                "tentang_uks.create_date",
                "jb.nama AS jenis_tentang_uks",
                "kb.nama AS kategori_tentang_uks",
                "sb.nama AS status_tentang_uks"
            )
            ->leftJoin("ref.jenis_berita AS jb", "tentang_uks.jenis_tentang_uks_id", "=", "jb.jenis_berita_id")
            ->leftJoin("ref.kategori_berita AS kb", "tentang_uks.kategori_tentang_uks_id", "=", "kb.kategori_berita_id")
            ->leftJoin("ref.status_berita AS sb", "tentang_uks.status_tentang_uks_id", "=", "sb.status_berita_id")
            ->orderBy("tentang_uks.create_date", "DESC")
            ->where('tentang_uks.soft_delete', 0);

        if($cari != ""){ $tentang_uks->where("tentang_uks.judul", "like", "%{$cari}%"); }
        if($request->jenis_tentang_uks_id != ""){ $tentang_uks->where("tentang_uks.jenis_tentang_uks_id", $request->jenis_tentang_uks_id); }
        if($request->kategori_tentang_uks_id != ""){ $tentang_uks->where("tentang_uks.kategori_tentang_uks_id", $request->kategori_tentang_uks_id); }
        if($request->status_tentang_uks_id != ""){ $tentang_uks->where("tentang_uks.status_tentang_uks_id", $request->status_tentang_uks_id); }

        $count_all = $tentang_uks->count();
        $tentang_ukss = $tentang_uks->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($tentang_ukss),
            'count_all' => $count_all,
            'rows'      => $tentang_ukss
        ];
    }

    public function get_pertentang_uks(Request $request)
    {
        $tentang_uks_id = $request->tentang_uks_id;
        $pertentang_uks = DB::table('tentang_uks')->where('tentang_uks_id', $tentang_uks_id)->first();

        return (array)$pertentang_uks;
    }

    public function add(Request $request)
    {
        $data = $request->all('judul', 'slug', 'konten_tentang_uks');
        $parsed = JWTAuth::getPayload()->toArray();
        $pengguna_id = $parsed['sub'];

        $uuid = (string)Str::uuid();
        $data['tentang_uks_id'] = $uuid;
        $data['soft_delete'] = "0";
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['updater_id'] = $pengguna_id;

        if($request->images){
            $data['images'] = $request->images;
        }

        $create = DB::table('tentang_uks')->insert($data);

        return $status = $create ? 'success' : 'error';

        // return [
        //     'status' => $status,
        //     'id' => $uuid
        // ];
    }

    public function update(Request $request)
    {
        $data = $request->all('judul', 'slug', 'konten_tentang_uks');
        
        if($request->images){
            $data['images'] = $request->images;
        }

        $update = DB::table('tentang_uks')->where('tentang_uks_id', $request->tentang_uks_id)->update($data);
        return $update ? 'success' : 'error';
    }

    public function delete(Request $request)
    {
        $delete = DB::table('tentang_uks')->where('tentang_uks_id', $request->tentang_uks_id)->update(['soft_delete' => 1]);
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

        $upload = Storage::putFileAs('tentang_uksThumbnail', $request->file('image'), $name);

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
        $destinationPath = 'storage/tentang_uksConten';
        // $upload = $file->move($destinationPath,$file->getClientOriginalName());

        $upload = Storage::putFileAs('tentang_uksConten', $request->file('image'), $name);

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

    // GET REF tentang_uks
    public function getSelectTentanguks(Request $request)
    {
        return [
            'status_tentang_uks' => $this->getStatusBerita(),
            'kategori_tentang_uks' => $this->getKategoriBerita(),
            'jenis_tentang_uks' => $this->getJenisBerita()
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
}
