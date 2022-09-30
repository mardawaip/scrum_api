<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class KategoriFaqController extends Controller
{
    public function get(Request $request)
    {
        $kategorifaq = DB::table('ref.kategori_faq')->select('kategori_faq_id AS id', '*')->where('soft_delete', 0)->orderby('create_date', 'DESC');

        if($request->cari){
            $kategorifaq->where('nama', $request->cari);
        }

        $kategorifaq = $kategorifaq->get();

        return ['rows' => $kategorifaq, 'count' => count($kategorifaq)];
    }

    public function add(Request $request)
    {
        $lastId = DB::table('ref.kategori_faq')->orderby('kategori_faq_id', 'DESC')->first();
        $data = [
            'kategori_faq_id' => intval($lastId->kategori_faq_id) + 1,
            'nama' => $request->nama,
            'create_date' => date("Y-m-d H:i:s"),
            'last_update' => date("Y-m-d H:i:s"),
            'soft_delete' => 0,
        ];

        $kategorifaq = DB::table('ref.kategori_faq')->insert($data);

        return $kategorifaq ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $kategorifaq = DB::table('ref.kategori_faq')->where('kategori_faq_id', $request->kategori_faq_id)->update(['nama' => $request->nama]);
        return $kategorifaq ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $kategorifaq = DB::table('ref.kategori_faq')->where('kategori_faq_id', $request->kategori_faq_id)->update(['soft_delete' => 1]);
        return $kategorifaq ? "success" : "error";
    }

    public function getperkategorifaq(Request $request)
    {
        $kategorifaq = DB::table('ref.kategori_faq')->where('kategori_faq_id', $request->kategori_faq_id)->where('soft_delete', 0)->first();

        return (array)$kategorifaq;
    }
}