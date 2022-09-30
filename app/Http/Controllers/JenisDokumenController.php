<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class JenisDokumenController extends Controller
{
    public function get(Request $request)
    {
        $jenisdokumen = DB::table('ref.jenis_dokumen')->select('jenis_dokumen_id AS id', '*')->where('soft_delete', 0)->orderby('sorting', 'DESC');

        if($request->cari){
            $jenisdokumen->where('nama', $request->cari);
        }

        $jenisdokumen = $jenisdokumen->get();

        return ['rows' => $jenisdokumen, 'count' => count($jenisdokumen)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.jenis_dokumen')->orderby('jenis_dokumen_id', 'DESC')->first();
        $data = [
            'jenis_dokumen_id' => intval($lastId->jenis_dokumen_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0,
            'sorting' => $request->sorting
        ];

        $jenisdokumen = DB::table('ref.jenis_dokumen')->insert($data);

        return $jenisdokumen ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $jenisdokumen = DB::table('ref.jenis_dokumen')->where('jenis_dokumen_id', $request->jenis_dokumen_id)->update(['nama' => $request->nama]);
        return $jenisdokumen ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $jenisdokumen = DB::table('ref.jenis_dokumen')->where('jenis_dokumen_id', $request->jenis_dokumen_id)->update(['soft_delete' => 1]);
        return $jenisdokumen ? "success" : "error";
    }

    public function getperjenisdokumen(Request $request)
    {
        $jenisdokumen = DB::table('ref.jenis_dokumen')->where('jenis_dokumen_id', $request->jenis_dokumen_id)->where('soft_delete', 0)->first();

        return (array)$jenisdokumen;
    }
}
