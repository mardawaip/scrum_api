<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class typeinputController extends Controller
{
    public function get(Request $request)
    {
        $typeinput = DB::table('ref.type_input')->select('type_input_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $typeinput->where('nama', $request->cari);
        }

        $typeinput = $typeinput->get();

        return ['rows' => $typeinput, 'count' => count($typeinput)];
    }

    public function add(Request $request)
    {
        $typeInputId = $request->type_input_id;
        // $lastId = DB::table('ref.type_input')->orderby('type_input_id', 'DESC')->first();
        // $lastId = $lastId ? $lastId->type_input_id:0; 
        $data = [
            'type_input_id' => $typeInputId,
            'nama' => $request->nama,
            'type_kolom' => $request->typekolom,
            'soft_delete' => 0,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s")
        ];

        $typeinput = DB::table('ref.type_input')->insert($data);

        return $typeinput ? "success" : "error";
    }

    public function edit(Request $request)
    {
        // $typeInputId = ->where('type_input_id, $request->type_input_id')->update(['nama' => $request->nama]);
        $typeinput = DB::table('ref.type_input')->where('type_input_id', $request->type_input_id)->update(['nama' => $request->nama]);
        return $typeinput ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $typeinput = DB::table('ref.type_input')->where('type_input_id', $request->type_input_id)->update(['soft_delete' => 1]);
        return $typeinput ? "success" : "error";
    }

    public function getpertypeinput(Request $request)
    {
        $typeinput = DB::table('ref.type_input')->where('type_input_id', $request->type_input_id)->first();

        return (array)$typeinput;
    }
}
