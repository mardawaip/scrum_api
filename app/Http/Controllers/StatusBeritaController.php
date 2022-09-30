<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class StatusBeritaController extends Controller
{
    public function get(Request $request)
    {
        $statusberita = DB::table('ref.status_berita')->select('status_berita_id AS id', '*')->where('soft_delete', 0)->orderby('status_berita_id', 'DESC');

        if($request->cari){
            $statusberita->where('nama', $request->cari);
        }

        $statusberita = $statusberita->get();

        return ['rows' => $statusberita, 'count' => count($statusberita)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.status_berita')->orderby('status_berita_id', 'DESC')->first();
        $data = [
            'status_berita_id' => intval($lastId->status_berita_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0
        ];

        $statusberita = DB::table('ref.status_berita')->insert($data);

        return $statusberita ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $statusberita = DB::table('ref.status_berita')->where('status_berita_id', $request->status_berita_id)->update(['nama' => $request->nama]);
        return $statusberita ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $statusberita = DB::table('ref.status_berita')->where('status_berita_id', $request->status_berita_id)->update(['soft_delete' => 1]);
        return $statusberita ? "success" : "error";
    }

    public function getperstatusberita(Request $request)
    {
        $statusberita = DB::table('ref.status_berita')->where('status_berita_id', $request->status_berita_id)->where('soft_delete', 0)->first();

        return (array)$statusberita;
    }
}
