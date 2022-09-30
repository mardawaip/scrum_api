<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use JWTAuth;
use DB;

class ProgramController extends Controller
{
    public function get_program(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 12;
        $program = DB::table('program')
            ->select(
                "program.judul",
                "program.slug",
                "program.images",
                "program.konten AS description"
            )
            ->where('status_program_id', 1)
            ->where('soft_delete', 0)
            ->orderBy('create_date', 'DESC')
            ->limit($limit)
            ->get();

        $i = 0;
        foreach ($program as $opt) {
            $program[$i]->description = substr(strip_tags($opt->description), 0, 100);
            $i++;
        }

        return [
            'rows' => $program,
            'count' => count($program)
        ];
    }

    public function get_program_detail(Request $request, $slug)
    {
        if(in_array($slug, ['pendidikan-kesehatan', 'pelayanan-kesehatan', 'pebinaan-lingkungan-sekolah-sehat'])){
            $getId = DB::table('auth.menu AS menu')->where('kode', $slug)->first()->menu_id;
            $listMenu = DB::table('auth.menu AS menu')
                ->select(
                    'menu.kode',
                    'menu.url',
                    'menu.title',
                    'program.konten AS description'
                )
                ->leftJoin('program', 'menu.kode', '=', 'program.slug')
                ->where('menu.induk_menu_id', $getId)
                ->get();

             $i = 0;
            foreach ($listMenu as $opt) {
                $listMenu[$i]->description = substr(strip_tags($opt->description), 0, 55);
                $i++;
            }

            return [
                'rows' => [
                    'id' => $getId,
                    'judul' => str_replace("-", " ", $slug),
                    'konten' => $listMenu
                ],
                'label' => []
            ];
        }else{
            $program = DB::table('program')->select("slug AS id", "konten AS konten",  "*")->where('slug', $slug)->first();
            $label = DB::table('program')
                ->select('slug AS value', 'judul AS label')
                ->where('soft_delete', 0)
                ->orderby('sorting', 'ASC')
                ->get();

            return [
                'rows' => $program,
                'label' => $label
            ];
        }
    }

    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;

        $program = DB::table('program')
            ->select(
                "program.program_id AS id",
                "program.program_id",
                "program.judul",
                "program.slug",
                "program.create_date",
                "jb.nama AS jenis_program",
                "kb.nama AS kategori_program",
                "sb.nama AS status_program"
            )
            ->leftJoin("ref.jenis_berita AS jb", "program.jenis_id", "=", "jb.jenis_berita_id")
            ->leftJoin("ref.kategori_berita AS kb", "program.kategori_id", "=", "kb.kategori_berita_id")
            ->leftJoin("ref.status_berita AS sb", "program.status_id", "=", "sb.status_berita_id")
            ->orderBy("program.create_date", "DESC")
            ->where('program.soft_delete', 0);

        if($cari != ""){ $program->where("program.judul", "like", "%{$cari}%"); }
        if($request->jenis_program_id != ""){ $program->where("program.jenis_id", $request->jenis_program_id); }
        if($request->kategori_program_id != ""){ $program->where("program.kategori_id", $request->kategori_program_id); }
        if($request->status_program_id != ""){ $program->where("program.status_id", $request->status_program_id); }

        $count_all = $program->count();
        $programs = $program->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($programs),
            'count_all' => $count_all,
            'rows'      => $programs
        ];
    }

    public function get_perprogram(Request $request)
    {
        $program_id = $request->program_id;
        $perprogram = DB::table('program')->where('program_id', $program_id)->first();

        return (array)$perprogram;
    }

    public function add(Request $request)
    {
        $data = $request->all('judul', 'slug', 'konten');
        $parsed = JWTAuth::getPayload()->toArray();
        $pengguna_id = $parsed['sub'];

        $uuid = (string)Str::uuid();
        $data['program_id'] = $uuid;
        $data['soft_delete'] = "0";
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['updater_id'] = $pengguna_id;

        if($request->images){
            $data['images'] = $request->images;
        }

        $create = DB::table('program')->insert($data);

        return $status = $create ? 'success' : 'error';

        // return [
        //     'status' => $status,
        //     'id' => $uuid
        // ];
    }

    public function update(Request $request)
    {
        $data = $request->all('judul', 'slug', 'konten');
        
        if($request->images){
            $data['images'] = $request->images;
        }

        $update = DB::table('program')->where('program_id', $request->program_id)->update($data);
        return $update ? 'success' : 'error';
    }

    public function delete(Request $request)
    {
        $delete = DB::table('program')->where('program_id', $request->program_id)->update(['soft_delete' => 1]);
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

        $upload = Storage::putFileAs('programThumbnail', $request->file('image'), $name);

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
        $destinationPath = 'storage/programConten';
        // $upload = $file->move($destinationPath,$file->getClientOriginalName());

        $upload = Storage::putFileAs('programConten', $request->file('image'), $name);

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

    // GET REF program
    public function getSelect(Request $request)
    {
        return [
            'status_program' => $this->getStatusBerita(),
            'kategori_program' => $this->getKategoriBerita(),
            'jenis_program' => $this->getJenisBerita()
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
