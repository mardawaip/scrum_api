<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class KategoriProgramController extends Controller
{
    public function get(Request $request)
    {
        $kategoriprogram = DB::table('ref.kategori_program')->select('kategori_program_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $kategoriprogram->where('nama', $request->cari);
        }

        $kategoriprogram = $kategoriprogram->get();

        return ['rows' => $kategoriprogram, 'count' => count($kategoriprogram)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.kategori_program')->orderby('kategori_program_id', 'DESC')->first();
        $lastId = $lastId ? $lastId->kategori_program_id:0; 
        $data = [
            'kategori_program_id' => intval($lastId) + 1,
            'nama' => $request->nama,
            'soft_delete' => 0,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s")
        ];

        $kategoriprogram = DB::table('ref.kategori_program')->insert($data);

        return $kategoriprogram ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $kategoriprogram = DB::table('ref.kategori_program')->where('kategori_program_id', $request->kategori_program_id)->update(['nama' => $request->nama]);
        return $kategoriprogram ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $kategoriprogram = DB::table('ref.kategori_program')->where('kategori_program_id', $request->kategori_program_id)->update(['soft_delete' => 1]);
        return $kategoriprogram ? "success" : "error";
    }

    public function getperkategoriprogram(Request $request)
    {
        $kategoriprogram = DB::table('ref.kategori_program')->where('kategori_program_id', $request->kategori_program_id)->first();

        return (array)$kategoriprogram;
    }
}
