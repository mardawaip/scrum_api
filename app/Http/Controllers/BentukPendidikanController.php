<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class BentukPendidikanController extends Controller
{
    public function get(Request $request)
    {
        $bentukpendidikan = DB::table('ref.bentuk_pendidikan')->select('bentuk_pendidikan_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $bentukpendidikan->where('nama', $request->cari);
        }

        $bentukpendidikan = $bentukpendidikan->get();

        return ['rows' => $bentukpendidikan, 'count' => count($bentukpendidikan)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.bentuk_pendidikan')->orderby('bentuk_pendidikan_id', 'DESC')->first();
        $data = [
            'bentuk_pendidikan_id' => intval($lastId->bentuk_pendidikan_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0,
            'expired_date' => date("Y-m-d H:i:s"),
            'updater_id' => $request->updater_id,
            'last_sync' => date("Y-m-d H:i:s")
        ];

        $bentukpendidikan = DB::table('ref.bentuk_pendidikan')->insert($data);

        return $bentukpendidikan ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $bentukpendidikan = DB::table('ref.bentuk_pendidikan')->where('bentuk_pendidikan_id', $request->bentuk_pendidikan_id)->update(['nama' => $request->nama]);
        return $bentukpendidikan ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $bentukpendidikan = DB::table('ref.bentuk_pendidikan')->where('bentuk_pendidikan_id', $request->bentuk_pendidikan_id)->update(['soft_delete' => 1]);
        return $bentukpendidikan ? "success" : "error";
    }

    public function getperbentukpendidikan(Request $request)
    {
        $bentukpendidikan = DB::table('ref.bentuk_pendidikan')->where('bentuk_pendidikan_id', $request->bentuk_pendidikan_id)->first();
        return (array)$bentukpendidikan;
    }
}
