<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class KategoriBeritaController extends Controller
{
    public function get(Request $request)
    {
        $kategoriberita = DB::table('ref.kategori_berita')->select('kategori_berita_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $kategoriberita->where('nama', $request->cari);
        }

        $kategoriberita = $kategoriberita->get();

        return ['rows' => $kategoriberita, 'count' => count($kategoriberita)];
    }

    public function add(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = [
            'kategori_berita_id' => $uuid,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0
        ];

        $kategoriberita = DB::table('ref.kategori_berita')->insert($data);

        return $kategoriberita ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $kategoriberita = DB::table('ref.kategori_berita')->where('kategori_berita_id', $request->kategori_berita_id)->update(['nama' => $request->nama]);
        return $kategoriberita ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $kategoriberita = DB::table('ref.kategori_berita')->where('kategori_berita_id', $request->kategori_berita_id)->update(['soft_delete' => 1]);
        return $kategoriberita ? "success" : "error";
    }

    public function getperkategoriberita(Request $request)
    {
        $kategoriberita = DB::table('ref.kategori_berita')->where('kategori_berita_id', $request->kategori_berita_id)->first();

        return (array)$kategoriberita;
    }
}
