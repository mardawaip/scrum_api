<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class StratifikasiuksController extends Controller
{
    public function get_wilayah(Request $request)
    {
        $db = DB::table('report.per_provinsi_v2')
        ->orderBy('persentase', "DESC")
        ->get();

        return [
            'rows' => $db
        ];
    }

    public function get_wilayah_kab(Request $request)
    {
        $db = DB::table('report.per_kabupaten')
        ->orderBy('persentase', "DESC")
        ->get();

        return [
            'rows' => $db
        ];
    }
}
