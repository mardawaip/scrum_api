<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use JWTAuth;
use DB;

class PublikasiController extends Controller
{
    public function get_publikasi(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 9999;
        $offset = ($page * $limit)-$limit;
        $jenis_dokumen_id = $request->jenis_dokumen_id ? $request->jenis_dokumen_id : '';
        $search = $request->search ? $request->search : '';
        $halaman = $request->halaman ? $request->halaman : 'buku-panduan';

        $publikasi = DB::table('publikasi')
            ->select(
                'publikasi.publikasi_id AS id',
                'publikasi.*',
                'jd.nama AS jenis_dokumen'
            )
            ->leftJoin('ref.jenis_dokumen AS jd', 'publikasi.jenis_dokumen_id', '=', 'jd.jenis_dokumen_id')
            ->where('publikasi.soft_delete', 0)
            ->where('publikasi.halaman', $halaman)
            ->orderBy('publikasi.create_date', 'DESC');

            if($request->jenis_dokumen_id){
                $publikasi->where('publikasi.jenis_dokumen_id', $jenis_dokumen_id);
            }

            if($request->search){
                $publikasi->where('publikasi.nama','like', "%{$search}%");
            }

            $count = $publikasi->count();
            $rows = $publikasi->offset($offset)->limit($limit)->get();

            $i = 0;
            foreach ($rows as $key) {
                $files = DB::table('files')->where('table', 'publikasi')->where('header_id', $key->publikasi_id)->orderBy('date', 'DESC')->get();
                $rows[$i]->files = $files;
                $i++;
            }

            $select['jenis_dokumen'] = DB::table('ref.jenis_dokumen')->select("jenis_dokumen_id AS value", "nama AS label")->orderby('sorting', 'ASC')->where('soft_delete', 0)->get();

        return [
            'count' => $count,
            'rows' => $rows,
            'select' => $select
        ];
    }

    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;
        $halaman = $request->halaman ? $request->halaman : '';

        $publikasi = DB::table('publikasi')
            ->select(
                'publikasi.publikasi_id AS id',
                'publikasi.*',
                'jd.nama AS jenis_dokumen'                
            )
            ->leftJoin('ref.jenis_dokumen AS jd', 'publikasi.jenis_dokumen_id', '=', 'jd.jenis_dokumen_id')
            ->where('publikasi.soft_delete', 0)
            ->where('publikasi.halaman', $halaman)
            ->orderby('publikasi.create_date', 'DESC');

        if($cari != ""){ $publikasi->where("publikasi.nama", "like", "%{$cari}%"); }

        $count_all = $publikasi->count();
        $publikasis = $publikasi->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        return [
            'count'     => count($publikasis),
            'count_all' => $count_all,
            'rows'      => $publikasis
        ];
    }

    public function add(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();
        unset($data['files']);

        if($request->halaman == 'video'){
            $kode_youtube = str_replace("https://www.youtube.com/watch?v=", "", $request->link_youtube);
            $data['link_youtube'] = 'https://www.youtube.com/watch?v='.$kode_youtube;
            $data['thumbnail'] = 'https://i.ytimg.com/vi/'.$kode_youtube.'/hqdefault.jpg';
            $data['embed_youtube'] = 'https://www.youtube.com/embed/'.$kode_youtube;

        }elseif($request->halaman == 'buku-panduan'){

        }elseif($request->halaman == 'infografis'){

        }else{

        }

        $data['publikasi_id'] = $uuid;
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['last_update'] = date("Y-m-d H:i:s");
        $data['soft_delete'] = 0;

        if($request->logo){
            $data['logo'] = $request->logo;
        }

        $publikasi = DB::table('publikasi')->insert($data);

        $status = $publikasi ? "success" : "error";
        return ['status' => $status, 'id' => $uuid];
    }

    public function edit(Request $request)
    {
        $data = $request->all();
        unset($data['files']);
        unset($data['logo']);
        $data['last_update'] = date("Y-m-d H:i:s");

        if($request->halaman == 'video'){
            $kode_youtube = str_replace("https://www.youtube.com/watch?v=", "", $request->link_youtube);
            $data['link_youtube'] = 'https://www.youtube.com/watch?v='.$kode_youtube;
            $data['thumbnail'] = 'https://i.ytimg.com/vi/'.$kode_youtube.'/hqdefault.jpg';
            $data['embed_youtube'] = 'https://www.youtube.com/embed/'.$kode_youtube;

        }elseif($request->halaman == 'buku-panduan'){

        }elseif($request->halaman == 'infografis'){

        }else{

        }

        if($request->logo != null){
            $data['logo'] = $request->logo;
        }

        $publikasi = DB::table('publikasi')->where('publikasi_id', $request->publikasi_id)->update($data);
        $status = $publikasi ? "success" : "error";
        return ['status' => $status, 'id' => $request->publikasi_id];
    }

    public function delete(Request $request)
    {
        $publikasi = DB::table('publikasi')->where('publikasi_id', $request->publikasi_id)->update(['soft_delete' => 1]);
        return $publikasi ? "success" : "error";
    }

    public function getperdata(Request $request)
    {
        $publikasi = DB::table('publikasi')->where('publikasi_id', $request->publikasi_id)->first();
        $id = $publikasi->publikasi_id;

        $files = DB::table('files')->where('table', 'publikasi')->where('header_id', $id)->get();
        $publikasi->files = $files;

        return (array)$publikasi;
    }

    public function getfileperdata(Request $request)
    {
        $id = $request->publikasi_id;
        $files = DB::table('files')->where('table', 'publikasi')->where('header_id', $id)->get();

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

    public function getSelect($value='')
    {
        return [
            'bidang' => $this->getBidang(),
            'jenis_dokumen' => $this->getJenisDokumen()
        ];
    }

    public function getJenisDokumen()
    {
        $jenis_dokumen = DB::table('ref.jenis_dokumen')->select("jenis_dokumen_id", "nama")->where('soft_delete', 0)->get();
        return $jenis_dokumen;
    }

    public function getBidang()
    {
        $bidang = DB::table('ref.bidang')->select("bidang_id", "nama")->where('soft_delete', 0)->get();
        return $bidang;
    }

    public function upload_cover(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();

        $file = $data['files'];
        $publikasi_id = $request->publikasi_id;

        if(!$file){
            return $request->all();
        }

        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();
        $size = $file->getSize(); //size

        $format = ['JPEG', 'JPG', 'PNG', 'SVG','jpeg', 'jpg', 'png', 'svg'];
        if(!in_array($ext, $format)){ return response(['msg' => "Format tidak didukung"]); }
        
        $imageName = time() . '.' . $ext;
        $path = Storage::disk('is3_spaces')->put('manajemen_uks/files/publikasi', $file, [
            'visibility' => 'public',
            'mimetype' => $file->getClientMimeType(),
        ]);

        $db = DB::table('publikasi')->where('publikasi_id', $publikasi_id)->update(['cover' => $path, 'last_update' => date("Y-m-d") ]);

        $msg = $db ? 'Success Upload File' : 'Error Upload File';
    }

    public function upload_files(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();

        $file = $data['files'];
        $publikasi_id = $request->publikasi_id;

        if(!$file){
            return $request->all();
        }

        $ext = $file->getClientOriginalExtension();
        $name = $file->getClientOriginalName();
        $size = $file->getSize(); //size

        $format = ['JPEG', 'JPG', 'PNG', 'SVG','jpeg', 'jpg', 'png', 'svg', 'pdf', 'docx', 'doc', 'docm'];
        if(!in_array($ext, $format)){ return response(['msg' => "Format tidak didukung"]); }
        
        $imageName = time() . '.' . $ext;
        $path = Storage::disk('is3_spaces')->put('manajemen_uks/files/publikasi', $file, [
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
            'header_id' => $publikasi_id,
            'table' => 'publikasi',
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
