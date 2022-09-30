<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class JenisMenuController extends Controller 
{
    public function get(Request $request)
    {
        $jenismenu = DB::table('ref.jenis_menu')->select('*')->where('soft_delete', 0)->orderby('value', 'DESC');

        if($request->cari){
            $jenismenu->where('nama', $request->cari);
        }

        $jenismenu = $jenismenu->get();

        return ['rows' => $jenismenu, 'count' => count($jenismenu)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.jenis_menu')->orderby('value', 'DESC')->first();
        $data = [
            'value' => $request->value,
            'label' => $request->label,
            'soft_delete' => 0
        ];

        $jenismenu = DB::table('ref.jenis_menu')->insert($data);

        return $jenismenu ? "success" : "error";
    }
    
    public function edit(Request $request)
    {
        $jenismenu = DB::table('ref.jenis_menu')->update(['value' => $request->value]);
        $jenismenu = DB::table('ref.jenis_menu')->update(['label' => $request->label]);
        return $jenismenu ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $jenismenu = DB::table('ref.jenis_menu')->where('value', $request->value)->update(['soft_delete' => 1]);
        return $jenismenu ? "success" : "error";
    }

    public function getperjenismenu(Request $request)
    {
        $jenismenu = DB::table('ref.jenis_menu')->where('value', $request->value)->first();
        return (array)$jenismenu;
    }
}