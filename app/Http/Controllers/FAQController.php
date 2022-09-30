<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class FAQController extends Controller
{
    public function getdata(Request $request)
    {
        $kategori_faq = json_decode($request->kategori);
        $faq = DB::table('faq')
        ->select(
            "faq.*",
            "kf.nama AS kategori"
        )
        ->leftJoin("ref.kategori_faq AS kf", "faq.kategori_id", "=", "kf.kategori_faq_id")
        ->where('faq.soft_delete', 0)
        ->whereIn('faq.kategori_id', $kategori_faq)
        ->limit(5)
        ->orderby('faq.create_date', 'DESC');

        if($request->cari){
            $faq->where('faq.pertanyaan', $request->cari);
            // ->orWhere('faq.jawaban', 'LIKE', "%$request->cari%");
        }

        $faq = $faq->get();

        return [
            'rows' => $faq,
            'count' => count($faq)
        ];
    }

    public function getKategorifaq()
    {
        $kategori = DB::table('ref.kategori_faq AS kf')
            ->select(
                "kf.*",
                DB::raw("(SELECT COUNT(*) FROM faq WHERE kategori_id = kf.kategori_faq_id) AS count")
            )
            ->where('soft_delete', 0)
            ->get();

        $selected = [];

        foreach ($kategori as $key) {
            $selected[] = intval($key->kategori_faq_id);
        }

        return [
            'rows' => $kategori,
            'selected' => $selected
        ];
    }

    public function get(Request $request)
    {
        $faq = DB::table("faq")
        ->select(
            "faq.faq_id AS id",
            "kategori_faq.nama AS kategori",
            "faq.*"
        )
        ->leftJoin("ref.kategori_faq AS kategori_faq", "faq.kategori_id", "=", "kategori_faq.kategori_faq_id")
        ->where("faq.soft_delete", 0)
        ->orderby("faq.create_date", "DESC");

        if($request->cari){
            $faq->where('pertanyaan', $request->cari);
        }

        $faq = $faq->get();

        return ['rows' => $faq, 'count' => count($faq)];
    }

    public function add(Request $request)
    {
        $uuid = (string)Str::uuid();
        $data = $request->all();
        unset($data['files']);

        $data['faq_id'] = $uuid;
        $data['create_date'] = date("Y-m-d H:i:s");
        $data['last_update'] = date("Y-m-d H:i:s");
        $data['soft_delete'] = 0;

        if($request->logo){
            $data['logo'] = $request->logo;
        }

        $faq = DB::table('faq')->insert($data);

        return $faq ? "success" : "error";
    }

    public function edit(Request $request)
    {
        $data = $request->all();
        unset($data['files']);
        unset($data['logo']);
        $data['last_update'] = date("Y-m-d H:i:s");

        if($request->logo != null){
            $data['logo'] = $request->logo;
        }

        $faq = DB::table('faq')->where('faq_id', $request->faq_id)->update($data);
        return $faq ? "success" : "error";
    }

    public function delete(Request $request)
    {
        $faq = DB::table('faq')->where('faq_id', $request->faq_id)->update(['soft_delete' => 1]);
        return $faq ? "success" : "error";
    }

    public function getperfaq(Request $request)
    {
        $faq = DB::table('faq')->where('faq_id', $request->faq_id)->first();

        return (array)$faq;
    }

    public function getSelectfaq($value='')
    {
        $kategori_faq = $this->getKategorifaq();
        
        return [
            'kategori_faq' => $kategori_faq['rows']
        ];
    }
}
