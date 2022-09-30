<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class JenisAgendaController extends Controller
{
    public function get(Request $request)
    {
        $jenisagenda = DB::table('ref.jenis_agenda')->select('jenis_agenda_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $jenisagenda->where('nama', $request->cari);
        }

        $jenisagenda = $jenisagenda->get();

        return ['rows' => $jenisagenda, 'count' => count($jenisagenda)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.jenis_agenda')->orderby('jenis_agenda_id', 'DESC')->first();
        $data = [
            'jenis_agenda_id' => intval($lastId->jenis_agenda_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0
        ];

        $jenisagenda = DB::table('ref.jenis_agenda')->insert($data);

        return $jenisagenda ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $jenisagenda = DB::table('ref.jenis_agenda')->where('jenis_agenda_id', $request->jenis_agenda_id)->update(['nama' => $request->nama]);
        return $jenisagenda ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $jenisagenda = DB::table('ref.jenis_agenda')->where('jenis_agenda_id', $request->jenis_agenda_id)->update(['soft_delete' => 1]);
        return $jenisagenda ? "success" : "error";
    }

    public function getperjenisagenda(Request $request)
    {
        $jenisagenda = DB::table('ref.jenis_agenda')->where('jenis_agenda_id', $request->jenis_agenda_id)->first();

        return (array)$jenisagenda;
    }
}
