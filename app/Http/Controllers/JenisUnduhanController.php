<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class JenisUnduhanController extends Controller
{
    public function get(Request $request)
    {
        $jenisunduhan = DB::table('ref.jenis_unduhan')->select('jenis_unduhan_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $jenisunduhan->where('nama', $request->cari);
        }

        $jenisunduhan = $jenisunduhan->get();

        return ['rows' => $jenisunduhan, 'count' => count($jenisunduhan)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.jenis_unduhan')->orderby('jenis_unduhan_id', 'DESC')->first();

        $data = [
            'jenis_unduhan_id' => intval($lastId->jenis_unduhan_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0
        ];

        $jenisunduhan = DB::table('ref.jenis_unduhan')->insert($data);

        return $jenisunduhan ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $jenisunduhan = DB::table('ref.jenis_unduhan')->where('jenis_unduhan_id', $request->jenis_unduhan_id)->update(['nama' => $request->nama]);
        return $jenisunduhan ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $jenisunduhan = DB::table('ref.jenis_unduhan')->where('jenis_unduhan_id', $request->jenis_unduhan_id)->update(['soft_delete' => 1]);
        return $jenisunduhan ? "success" : "error";
    }

    public function getperjenisunduhan(Request $request)
    {
        $jenisunduhan = DB::table('ref.jenis_unduhan')->where('jenis_unduhan_id', $request->jenis_unduhan_id)->first();

        return (array)$jenisunduhan;
    }
}
