<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class JenisBeritaController extends Controller
{
    public function get(Request $request)
    {
        $jenisberita = DB::table('ref.jenis_berita')->select('jenis_berita_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $jenisberita->where('nama', $request->cari);
        }

        $jenisberita = $jenisberita->get();

        return ['rows' => $jenisberita, 'count' => count($jenisberita)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.jenis_berita')->orderby('jenis_berita_id', 'DESC')->first();
        $data = [
            'jenis_berita_id' => intval($lastId->jenis_berita_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0
        ];

        $jenisberita = DB::table('ref.jenis_berita')->insert($data);

        return $jenisberita ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $jenisberita = DB::table('ref.jenis_berita')->where('jenis_berita_id', $request->jenis_berita_id)->update(['nama' => $request->nama]);
        return $jenisberita ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $jenisberita = DB::table('ref.jenis_berita')->where('jenis_berita_id', $request->jenis_berita_id)->update(['soft_delete' => 1]);
        return $jenisberita ? "success" : "error";
    }

    public function getperjenisberita(Request $request)
    {
        $jenisberita = DB::table('ref.jenis_berita')->where('jenis_berita_id', $request->jenis_berita_id)->first();

        return (array)$jenisberita;
    }
}
