<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use JWTAuth;
use DB;

class PraktekbaikController extends Controller
{
    public function get_praktekbaik(Request $request, $page)
    {
        $limit = $request->limit ? $request->limit : 12;
        $offset = ($page * $limit)-$limit;
        $cari = $request->cari;

        $from = date("Y-m-d", strtotime("2022-1-1"));
        $to = date("Y-m-d");

        $praktik_baik = DB::table('praktik_baik')
            ->select(
                "praktik_baik.judul",
                "praktik_baik.slug",
                "praktik_baik.images",
                "praktik_baik.konten AS description",
                "praktik_baik.tanggal_publis",
                "praktik_baik.tags"
            )
            ->where('praktik_baik.status_id', 1)
            ->where('praktik_baik.soft_delete', 0)
            ->whereBetween('praktik_baik.tanggal_publis', [$from, $to])
            ->orderBy('last_update', 'DESC');

        if(@$cari != ""){ $praktik_baik->where("praktik_baik.judul", "like", "%{$cari}%"); }
        $count_all = $praktik_baik->count();
        $rows = $praktik_baik->limit($limit)->offset($offset)->get();

        $i = 0;
        foreach ($rows as $opt) {
            $rows[$i]->description = substr(strip_tags($opt->description), 0, 100);
            $rows[$i]->tags = explode(",", $rows[$i]->tags);
            $i++;
        }

        return [
            'rows' => $rows,
            'count' => count($rows),
            'count_all' => $count_all,
            'date' => [$from, $to]
        ];
    }

    public function get_praktik_detail(Request $request, $slug)
    {
        $praktik_baik = DB::table('praktik_baik')->where('slug', $slug)->first();

        return (array)$praktik_baik;
    }

    public function get(Request $request)
    {
        $limit = $request->limit ? $request->limit : 10;
        $offset = $request->offset ? ($request->offset * $limit) : 0;
        $cari = $request->cari;

        $praktik_baik = DB::table('praktik_baik')
            ->select(
                "praktik_baik.praktik_baik_id AS id",
                "praktik_baik.praktik_baik_id",
                "praktik_baik.judul",
                "praktik_baik.slug",
                "praktik_baik.create_date",
                "praktik_baik.tanggal_publis",
                "praktik_baik.tags",
                "kb.nama AS kategori_artikel",
                "sb.nama AS status"
            )
            ->leftJoin("ref.kategori_artikel AS kb", "praktik_baik.kategori_artikel_id", "=", "kb.kategori_artikel_id")
            ->leftJoin("ref.status_berita AS sb", "praktik_baik.status_id", "=", "sb.status_berita_id")
            ->orderBy("praktik_baik.create_date", "DESC")
            ->where('praktik_baik.soft_delete', 0);

        if($cari != ""){ $praktik_baik->where("praktik_baik.judul", "like", "%{$cari}%"); }      
        if(!in_array($request->kategori_id, ["", "-"])){ $praktik_baik->where("praktik_baik.kategori_artikel_id", $request->kategori_id); }
        if(!in_array($request->status_id, ["", "-"])){ $praktik_baik->where("praktik_baik.status_id", $request->status_id); }

        $count_all = $praktik_baik->count();
        $praktik_baiks = $praktik_baik->limit($limit == 'all' ? $count_all : $limit)->offset($offset)->get();

        $i=0;
        foreach ($praktik_baiks as $key) {
            $praktik_baiks[$i]->tags = explode(",", $key->tags);
            $i++;
        }

        return [
            'count'     => count($praktik_baiks),
            'count_all' => $count_all,
            'rows'      => $praktik_baiks
        ];
    }

    public function get_perpraktik_baik(Request $request)
    {
        $praktik_baik_id = $request->praktik_baik_id;
        $perpraktik_baik = DB::table('praktik_baik')->where('praktik_baik_id', $praktik_baik_id)->first();

        return (array)$perpraktik_baik;
    }

    public function add(Request $request)
    {
        $data = $request->all('judul','konten','pengguna_id','kategori_artikel_id','status_id','tanggal_publis','images','updater_id','tags');
        $parsed = JWTAuth::getPayload()->toArray();
        $pengguna_id = $parsed['sub'];

        $uuid = (string)Str::uuid();
        $data['praktik_baik_id'] = $uuid;
        $data['slug'] = str_replace([" ", "/"], "-", $request->judul);
        $data['soft_delete'] = "0";
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['updater_id'] = $pengguna_id;
        $data['tags'] = implode(",", $request->tags);

        if($request->images){
            $data['images'] = $request->images;
        }

        $create = DB::table('praktik_baik')->insert($data);


        return $status = $create ? 'success' : 'error';
        // return ['status' => $status, 'id' => $uuid];
    }

    public function update(Request $request)
    {
        $data = $request->all('judul','konten','pengguna_id','kategori_artikel_id','status_id','tanggal_publis','images','updater_id','tags');
        $data['slug'] = str_replace([" ", "/"], "-", $request->judul);
        $data['tags'] = implode(",", $request->tags);
        
        if($request->images){
            $data['images'] = $request->images;
        }

        $update = DB::table('praktik_baik')->where('praktik_baik_id', $request->praktik_baik_id)->update($data);

        return $status = $update ? 'success' : 'error';
    }

    public function delete(Request $request)
    {
        $delete = DB::table('praktik_baik')->where('praktik_baik_id', $request->praktik_baik_id)->update(['soft_delete' => 1]);
        return $delete ? 'success' : 'error';
    }

    public function get_perdata(Request $request)
    {
        $praktik_baik_id = $request->praktik_baik_id;
        $perberita = DB::table('praktik_baik')->where('praktik_baik_id', $praktik_baik_id)->first();
        $perberita->tags = explode(",", $perberita->tags);

        return (array)$perberita;
    }

    // GET REF
    public function getSelect(Request $request)
    {
        return [
            'status' => $this->getStatus(),
            'kategori' => $this->getKategori(),
        ];
    }

    public function getStatus()
    {
        $statusBerita = DB::table("ref.status_berita")->select("status_berita_id AS value", "nama AS label")->where("soft_delete", 0)->get();
        return $statusBerita;
    }

    public function getKategori()
    {
        $KategoriBerita = DB::table("ref.kategori_artikel")->select("kategori_artikel_id AS value", "nama AS label")->where("soft_delete", 0)->get();
        return $KategoriBerita;
    }
}
