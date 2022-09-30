<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use File;
use Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class AppController extends Controller
{
    public function index($value='')
    {
    	return "<pre>UKS</pre>";
    }

    public function formatWidgets($title, $name, $count, $addCount, $link)
    {
        $format = [
            'title' => $title,
            'link' => $link,
            'data' => [
                'name' => $name,
                'count' => $count,
                'extra' => [
                    'name' => "Yesterday's overdue",
                    'count' => $addCount
                ]
            ]
        ];

        return $format;
    }

    public function getWidget()
    {
        $berita = DB::table('berita')->where('soft_delete', 0)->count();
        $agenda = DB::table('agenda')->where('soft_delete', 0)->count();
        $produk_hukum = DB::table('produk_hukum')->where('soft_delete', 0)->count();
        $publikasi = DB::table('publikasi')->where('soft_delete', 0)->count();

        return [
            $this->formatWidgets("Berita", "Konten", $berita, 0, '/manajemen-konten/berita'),
            $this->formatWidgets("Agenda", "Kegiatan", $agenda, 0, '/manajemen-konten/agenda'),
            $this->formatWidgets("Produk Hukum", "Berkas", $produk_hukum, 0, '/manajemen-konten/produk-hukum'),
            $this->formatWidgets("Publikasi", "Konten", $publikasi, 0, '/manajemen-konten/publikasi/buku-panduan'),
        ];
    }

    public function getSliders()
    {
        $db = DB::table('slider')->select('title', 'konten', 'images')->orderby('create_date', 'ASC')->where('soft_delete', 0)->get();

        return $db;
    }

    public function getSlider()
    {
        $db = DB::table('slider')->where('soft_delete', 0)->orderby('create_date', 'ASC')->get();

        return $db;
    }

    public function optWilayah(Request $request)
    {
        $id_level_wilayah = $request->id_level_wilayah;
        $mst_kode_wilayah = $request->mst_kode_wilayah;

        $cek = Cache::has("OPTWILAYAH::".$id_level_wilayah.'::'.$mst_kode_wilayah);
        if($cek){
            return Cache::get("OPTWILAYAH::".$id_level_wilayah.'::'.$mst_kode_wilayah); die;
        }

        $wilayah = DB::connection('sqlsrv')->table('ref.mst_wilayah')->select(DB::raw('RTRIM(kode_wilayah) AS value'), 'nama AS label');
        if($request->id_level_wilayah){
            $wilayah->where('id_level_wilayah', $request->id_level_wilayah);
        }

        if($request->mst_kode_wilayah){
            $wilayah->where('mst_kode_wilayah', $request->mst_kode_wilayah);
        }

        $rows = $wilayah->get();
        Cache::put("OPTWILAYAH::".$id_level_wilayah.'::'.$mst_kode_wilayah, $rows, 3600);
        return Cache::get("OPTWILAYAH::".$id_level_wilayah.'::'.$mst_kode_wilayah);
    }

    public function Wilayah(Request $request)
    {
        $wilayah = DB::connection('sqlsrv')->table('ref.mst_wilayah')->lock("WITH(NOLOCK)")->select("*", DB::raw('RTRIM(kode_wilayah) AS kode_wilayah'));
        $kodeWilayah = $request->kode_wilayah;
        $idLevelWilayah = $request->id_level_wilayah;
        
        if($idLevelWilayah && $kodeWilayah){
            $wilayah->where('id_level_wilayah', $idLevelWilayah)
            ->where('mst_kode_wilayah', $kodeWilayah);
        }else{
            if(!$kodeWilayah){
                $wilayah->where('kode_wilayah', '000000');
            }
        }

        if($kodeWilayah && !$idLevelWilayah){
            $wilayah->where('kode_wilayah', $kodeWilayah);
        }

        // $sql = $wilayah->tosql();
        $data = $wilayah->get();
        
        $return = [
            'total' => count($data),
            'rows' => $data,
            // 'sql' => $sql
        ];
        
        return $return;
    }

    public function GetMemuItem(Request $request)
    {
        $peranId = $request->peran_id;
        $cek = Cache::has("MenuItem::".$peranId);

        if($cek){
            return Cache::get("MenuItem::".$peranId);
        }

        $menu = DB::connection('sqlsrv_2')
            ->table('auth.menu_peran AS menu_peran')
            ->lock("WITH(NOLOCK)")
            ->select('menu.*')
            ->leftJoin('auth.menu AS menu', 'menu_peran.menu_id', '=', 'menu.menu_id')
            ->where([
                'menu.type' => 'item',
                'menu.soft_delete' => 0,
                'menu_peran.soft_delete' => 0,
                'menu_peran.peran_id' => $peranId
                ])
            ->orderBy('menu.title', 'ASC')
            ->orderBy('menu.nomor_urut', 'ASC');

        $menu->where('dashboard', "1");

        $rows = $menu->get();

        Cache::put("MenuItem::".$peranId, $rows, 3600);
        return Cache::get("MenuItem::".$peranId);
    }

    public function _json_test(Request $request)
    {
        $data = json_encode(['Text 1','Text 2','Text 3','Text 4','Text 5', 'Text 6']);
        $file = 'berita_file.json';
        $destinationPath=public_path()."/json/";
        if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
        File::put($destinationPath.$file,$data);
        return $destinationPath.$file;
    }

    public function _prefil(Request $request)
    {
        if($request->search === ""){
            return "Tidak ada pencarian";
        }

        $prefil = DB::connection('sqlsrv')
        ->table('tmp.generate_prefill')
        ->where('npsn', $request->search)
        ->get();
        return $prefil;
    }

    public function _download_prefil(Request $request)
    {
        // if(($request->nama_file == "") || (strlen($request->nama_file) != 8)){
        //     return response()->json(['error' => "NPSN ".$request->nama_file." salah"]);
        // }

        $prefil = DB::connection('sqlsrv')
        ->table('tmp.generate_prefill')
        ->where('nama_file', $request->nama_file)
        ->first();

        $destinationPath = public_path()."\..\..\..\..\\";
        $link = $destinationPath.$prefil->path;
        $fileContent = File::get($link);

        if (is_null($fileContent)) {
            return response()->json(['error' => 'Tidak ditemukan file prefil untuk NPSN: '.$request->npsn]);
        }

       return response($fileContent)
            ->header('Cache-Control', 'no-cache private')
            ->header('Content-Description', 'File Transfer')
            ->header('Content-Type', 'application/octet-stream')
            ->header('Content-length', strlen($fileContent))
            ->header('Content-Disposition', 'attachment; filename=' . $prefil->nama_file)
            ->header('Content-Transfer-Encoding', 'binary');
    }

    public function getRekapWilayah()
    {
        $return = [
            'propinsi' => DB::table('report.per_provinsi')->limit(10)->orderBy('persentase', 'DESC')->get(),
            'kabupaten' => DB::table('report.per_kabupaten')->limit(10)->orderBy('persentase', 'DESC')->get(),
        ];

        return $return;
    }

    public function simpan_slider(Request $request)
    {
        $data = $request->slider;
        $delete = $request->delete;
        $status = [];

        if(count($delete) != 0){
            $delete = DB::table('slider')->whereIn('slider_id', $delete)->update(['soft_delete' => 1]);
        }

        for ($i=0; $i < count($data) ; $i++) { 
            $dt = $data[$i];
            $slider_id = $dt['slider_id'];

            $cek = DB::table('slider')->where('slider_id', $slider_id)->count();

            if($cek == 1){
                $data0 = $dt;
                $data0['last_update'] = date("Y-m-d H:i:s");
                $fetch = DB::table('slider')->where('slider_id', $slider_id)->update($data0);
            }else{
                $data0 = $dt;
                $data0['create_date'] = date("Y-m-d H:i:s");
                $data0['soft_delete'] = 0;
                $fetch = DB::table('slider')->insert($data0);
            }
        }

        return $fetch ? "success" : "error";
    }
}