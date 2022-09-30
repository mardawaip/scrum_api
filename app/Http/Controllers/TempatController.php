<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class TempatController extends Controller
{
    public function get(Request $request)
    {
        $tempat = DB::table('ref.tempat')->select('tempat_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $tempat->where('nama', $request->cari);
        }

        $tempat = $tempat->get();

        return ['rows' => $tempat, 'count' => count($tempat)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.tempat')->orderby('tempat_id', 'DESC')->first();
        $lastId = $lastId ? $lastId->tempat_id:0;
        $data = [
            'tempat_id' => intval($lastId) + 1,
            'nama' => $request->nama,
            'soft_delete' => 0,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
        ];

        $tempat = DB::table('ref.tempat')->insert($data);

        return $tempat ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $tempat = DB::table('ref.tempat')->where('tempat_id', $request->tempat_id)->update(['nama' => $request->nama]);
        return $tempat ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $tempat = DB::table('ref.tempat')->where('tempat_id', $request->tempat_id)->update(['soft_delete' => 1]);
        return $tempat ? "success" : "error";
    }

    public function getpertempat(Request $request)
    {
        $tempat = DB::table('ref.tempat')->where('tempat_id', $request->tempat_id)->where('soft_delete', 0)->first();

        return (array)$tempat;
    }
}