<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use JWTAuth;
use DB;

class BeritaController extends Controller
{
    public function get_berita(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 12;
        $search = $request->search ? $request->search : '';
        $kategori = $request->kategori ? (explode(",", $request->kategori)) : [];
        $offset = ($page * $limit)-$limit;
        $from = date("Y-m-d", strtotime("2022-1-1"));
        $to = date("Y-m-d");

        $berita = DB::table('berita')
            ->select(
                "berita.judul",
                "berita.slug",
                "berita.images",
                "berita.konten_berita AS description",
                "berita.tanggal_publis",
                "jb.nama AS jenis_berita",
                "kb.nama AS kategori_berita",
                "sb.nama AS status_berita"
            )
            ->leftJoin("ref.jenis_berita AS jb", "berita.jenis_berita_id", "=", "jb.jenis_berita_id")
            ->leftJoin("ref.kategori_berita AS kb", "berita.kategori_berita_id", "=", "kb.kategori_berita_id")
            ->leftJoin("ref.status_berita AS sb", "berita.status_berita_id", "=", "sb.status_berita_id")
            ->where('berita.status_berita_id', 1)
            ->where('berita.soft_delete', 0)
            ->whereBetween('berita.tanggal_publis', [$from, $to])
            ->orderBy('berita.last_update', 'DESC');

        if($request->search){
            $berita->where('berita.judul', 'like', "%{$search}%");
        }

        if($request->kategori){
            $berita->whereIn('berita.kategori_berita_id', $kategori);
        }

        $count_all = $berita->count();
        $rows = $berita->limit($limit)->offset($offset)->get();

        $i = 0;
        foreach ($rows as $opt) {
            $rows[$i]->title = substr(strip_tags($opt->judul), 0, 90);
            $rows[$i]->description = substr(strip_tags($opt->description), 0, 256);
            $i++;
        }

        return [
            'rows' => $rows,
            'count' => count($rows),
            'count_all' => $count_all,
            'date' => [$from, $to],
            'select' => $this->getSelectBerita_()
        ];
    }

    public function get_berita_detail(Request $request, $slug)
    {
        $berita = DB::table('berita')
         ->select(
            // "berita.berita_id AS id",
            // "berita.berita_id",
            "berita.judul",
            // "berita.slug",
            "berita.konten_berita",
            // "berita.create_date",
            "berita.tanggal_publis",
            "jb.nama AS jenis_berita",
            "kb.nama AS kategori_berita",
            "sb.nama AS status_berita"
        )
        ->leftJoin("ref.jenis_berita AS jb", "berita.jenis_berita_id", "=", "jb.jenis_berita_id")
        ->leftJoin("ref.kategori_berita AS kb", "berita.kategori_berita_id", "=", "kb.kategori_berita_id")
        ->leftJoin("ref.status_berita AS sb", "berita.status_berita_id", "=", "sb.status_berita_id")
        ->where('berita.slug', $slug)
        ->where('berita.soft_delete', 0)
        ->first();

        return (array)$berita;
    }

    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;

        $berita = DB::table('berita')
            ->select(
                "berita.berita_id AS id",
                "berita.berita_id",
                "berita.judul",
                "berita.slug",
                "berita.create_date",
                "berita.tanggal_publis",
                "jb.nama AS jenis_berita",
                "kb.nama AS kategori_berita",
                "sb.nama AS status_berita"
            )
            ->leftJoin("ref.jenis_berita AS jb", "berita.jenis_berita_id", "=", "jb.jenis_berita_id")
            ->leftJoin("ref.kategori_berita AS kb", "berita.kategori_berita_id", "=", "kb.kategori_berita_id")
            ->leftJoin("ref.status_berita AS sb", "berita.status_berita_id", "=", "sb.status_berita_id")
            ->orderBy("berita.create_date", "DESC")
            ->where('berita.soft_delete', 0);

        if($cari != ""){ $berita->where("berita.judul", "like", "%{$cari}%"); }
        if($request->jenis_berita_id != ""){ $berita->where("berita.jenis_berita_id", $request->jenis_berita_id); }
        if($request->kategori_berita_id != ""){ $berita->where("berita.kategori_berita_id", $request->kategori_berita_id); }
        if($request->status_berita_id != ""){ $berita->where("berita.status_berita_id", $request->status_berita_id); }

        $count_all = $berita->count();
        $beritas = $berita->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($beritas),
            'count_all' => $count_all,
            'rows'      => $beritas
        ];
    }

    public function get_perberita(Request $request)
    {
        $berita_id = $request->berita_id;
        $perberita = DB::table('berita')->where('berita_id', $berita_id)->first();

        return (array)$perberita;
    }

    public function add(Request $request)
    {
        $data = $request->all('jenis_berita_id','judul','kategori_berita_id','konten_berita','status_berita_id', 'tanggal_publis');
        $parsed = JWTAuth::getPayload()->toArray();
        $pengguna_id = $parsed['sub'];

        $uuid = (string)Str::uuid();
        $data['berita_id'] = $uuid;
        $data['slug'] = str_replace([" ", "/"], "-", $request->judul);
        $data['soft_delete'] = "0";
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['last_update'] = date("Y-m-d H:i:s");
        $data['updater_id'] = $pengguna_id;

        if($request->images){
            $data['images'] = $request->images;
        }

        $create = DB::table('berita')->insert($data);


        return $status = $create ? 'success' : 'error';
        // return ['status' => $status, 'id' => $uuid];
    }

    public function update(Request $request)
    {
        $data = $request->all('jenis_berita_id','judul','kategori_berita_id','konten_berita','status_berita_id', 'tanggal_publis');
        $data['slug'] = str_replace([" ", "/"], "-", $request->judul);
        $data['last_update'] = date("Y-m-d H:i:s");
        
        if($request->images){
            $data['images'] = $request->images;
        }

        $update = DB::table('berita')->where('berita_id', $request->berita_id)->update($data);

        return $status = $update ? 'success' : 'error';
        // return ['status' => $status, 'id' => $request->berita_id];
    }

    public function delete(Request $request)
    {
        $delete = DB::table('berita')->where('berita_id', $request->berita_id)->update(['soft_delete' => 1]);
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

        $format = ['jpeg', 'jpg', 'png', 'svg', 'webp'];
        if(!in_array($ext, $format)){ return response(['msg' => "Format tidak didukung"]); }

        // $upload = Storage::putFileAs('beritaThumbnail', $request->file('image'), $name);
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
        $destinationPath = 'storage/beritaConten';
        // $upload = $file->move($destinationPath,$file->getClientOriginalName());

        // $upload = Storage::putFileAs('beritaConten', $request->file('image'), $name);
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
    public function getSelectBerita(Request $request)
    {
        return [
            'status_berita' => $this->getStatusBerita(),
            'kategori_berita' => $this->getKategoriBerita(),
            'jenis_berita' => $this->getJenisBerita()
        ];
    }

    public function getSelectBerita_()
    {
        return [
            'status_berita' => $this->getStatusBerita(),
            'kategori_berita' => $this->getKategoriBerita(),
            'jenis_berita' => $this->getJenisBerita()
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
