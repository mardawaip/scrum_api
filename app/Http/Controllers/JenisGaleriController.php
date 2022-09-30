<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class JenisGaleriController extends Controller
{
    public function get(Request $request)
    {
        $jenisgaleri = DB::table('ref.jenis_galeri')->select('jenis_galeri_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $jenisgaleri->where('nama', $request->cari);
        }

        $jenisgaleri = $jenisgaleri->get();

        return ['rows' => $jenisgaleri, 'count' => count($jenisgaleri)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.jenis_galeri')->orderby('jenis_galeri_id', 'DESC')->first();
        $data = [
            'jenis_galeri_id' => intval($lastId->jenis_galeri_id) + 1,
            'nama' => $request->nama,
            'soft_delete' => 0,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'label' => $request->label
        ];

        $jenisgaleri = DB::table('ref.jenis_galeri')->insert($data);

        return $jenisgaleri ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $jenisgaleri = DB::table('ref.jenis_galeri')->where('jenis_galeri_id', $request->jenis_galeri_id)->update(['nama' => $request->nama]);
        return $jenisgaleri ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $jenisgaleri = DB::table('ref.jenis_galeri')->where('jenis_galeri_id', $request->jenis_galeri_id)->update(['soft_delete' => 1]);
        return $jenisgaleri ? "success" : "error";
    }

    public function getperjenisgaleri(Request $request)
    {
        $jenisgaleri = DB::table('ref.jenis_galeri')->where('jenis_galeri_id', $request->jenis_galeri_id)->where('soft_delete', 0)->first();

        return (array)$jenisgaleri;
    }
}