<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class PeranController extends Controller
{
    public function get(Request $request)
    {
        $peran = DB::table('ref.peran')->select('peran_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $peran->where('nama', $request->cari);
        }

        $peran = $peran->get();

        return ['rows' => $peran, 'count' => count($peran)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.peran')->whereNotIn('peran_id', [99])->orderby('peran_id', 'DESC')->first();
        $data = [
            'peran_id' => intval($lastId->peran_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0
        ];

        $peran = DB::table('ref.peran')->insert($data);

        return $peran ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $peran = DB::table('ref.peran')->where('peran_id', $request->peran_id)->update(['nama' => $request->nama]);
        return $peran ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $peran = DB::table('ref.peran')->where('peran_id', $request->peran_id)->update(['soft_delete' => 1]);
        return $peran ? "success" : "error";
    }

    public function getperperan(Request $request)
    {
        $peran = DB::table('ref.peran')->where('peran_id', $request->peran_id)->first();

        return (array)$peran;
    }
}
