<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class JenisFaqController extends Controller
{
    public function get(Request $request)
    {
        $jenisfaq = DB::table('ref.jenis_faq')->select('jenis_faq_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $jenisfaq->where('nama', $request->cari);
        }

        $jenisfaq = $jenisfaq->get();

        return ['rows' => $jenisfaq, 'count' => count($jenisfaq)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.jenis_faq')->orderby('jenis_faq_id', 'DESC')->first();
        $data = [
            'jenis_faq_id' => intval($lastId->jenis_faq_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0
        ];

        $jenisfaq = DB::table('ref.jenis_faq')->insert($data);

        return $jenisfaq ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $jenisfaq = DB::table('ref.jenis_faq')->where('jenis_faq_id', $request->jenis_faq_id)->update(['nama' => $request->nama]);
        return $jenisfaq ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $jenisfaq = DB::table('ref.jenis_faq')->where('jenis_faq_id', $request->jenis_faq_id)->update(['soft_delete' => 1]);
        return $jenisfaq ? "success" : "error";
    }

    public function getperjenisfaq(Request $request)
    {
        $jenisfaq = DB::table('ref.jenis_faq')->where('jenis_faq_id', $request->jenis_faq_id)->first();

        return (array)$jenisfaq;
    }
}
