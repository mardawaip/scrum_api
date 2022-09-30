<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class KategoriArtikelController extends Controller
{
    public function get(Request $request)
    {
        $kategoriartikel = DB::table('ref.kategori_artikel')->select('kategori_artikel_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $kategoriartikel->where('nama', $request->cari);
        }

        $kategoriartikel = $kategoriartikel->get();

        return ['rows' => $kategoriartikel, 'count' => count($kategoriartikel)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.kategori_artikel')->orderby('kategori_artikel_id', 'DESC')->first();
        $lastId = $lastId ? $lastId->kategori_artikel_id:0;
        $data = [
            'kategori_artikel_id' => intval($lastId) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0
        ];

        $kategoriartikel = DB::table('ref.kategori_artikel')->insert($data);

        return $kategoriartikel ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $kategoriartikel = DB::table('ref.kategori_artikel')->where('kategori_artikel_id', $request->kategori_artikel_id)->update(['nama' => $request->nama]);
        return $kategoriartikel ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $kategoriartikel = DB::table('ref.kategori_artikel')->where('kategori_artikel_id', $request->kategori_artikel_id)->update(['soft_delete' => 1]);
        return $kategoriartikel ? "success" : "error";
    }

    public function getperkategoriartikel(Request $request)
    {
        $kategoriartikel = DB::table('ref.kategori_artikel')->where('kategori_artikel_id', $request->kategori_artikel_id)->first();

        return (array)$kategoriartikel;
    }
}
