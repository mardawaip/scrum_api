<?php

namespace App\Http\Controllers\RekapRedis;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller as Controller;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;


class RedisProgresController extends Controller
{
    public function index($value='')
    {
        Cache::put( "RaporMutu:1:020000", "top markotop", 3600);

    	return Cache::get("RaporMutu:1:020000");
    }

    public function rekapRaporMutuNasional(Request $request){
        $fetch = DB::connection('sqlsrv')
        ->table(DB::raw('ref.wilayah with(nolock)'))
        ->where('level_wilayah_id','=',0);

        $records = $fetch->get();

        foreach ($records as $key) {

            // ini adalah prosesnya
            $return = array();

            $kolom_kode_wilayah = 'prop.kode_wilayah as kode_wilayah';
            $kolom_nama = 'prop.nama as nama';
            $kolom_induk = 'negara.nama as induk';
            $group_kode_wilayah = 'prop.kode_wilayah';
            $group_nama = 'prop.nama';
            $group_induk = 'negara.nama';
            $param_kode_wilayah = 'prop.kode_wilayah';

            $fetch = DB::connection('sqlsrv')
            ->table(DB::raw('master_pmp with(nolock)'))
            ->join(DB::raw('sekolah with(nolock)'),'sekolah.sekolah_id','=','master_pmp.sekolah_id')
            ->join(DB::raw('ref.wilayah as kec with(nolock)'), 'kec.kode_wilayah', '=', DB::raw('left(sekolah.kode_wilayah,6)'))
            ->join(DB::raw('ref.wilayah as kab with(nolock)'), 'kab.kode_wilayah', '=', 'kec.induk_kode_wilayah')
            ->join(DB::raw('ref.wilayah as prop with(nolock)'), 'prop.kode_wilayah', '=', 'kab.induk_kode_wilayah')
            ->join(DB::raw('ref.wilayah as negara with(nolock)'), 'negara.kode_wilayah', '=', 'prop.induk_kode_wilayah')
            ->select(
                $kolom_kode_wilayah,
                $kolom_nama,
                $kolom_induk,
                DB::raw("AVG ( r16 ) AS r16"),
                DB::raw("AVG ( r17 ) AS r17"),
                DB::raw("AVG ( r18 ) AS r18"),
                DB::raw("AVG ( r19 ) AS r19")
            )
            ->where('level','=','parent')
            ->groupBy($group_kode_wilayah)
            ->groupBy($group_nama)
            ->groupBy($group_induk);
            ;

            $return['total'] = sizeof($fetch->get());
            
            $fetch->skip(0)->take(70000);
            
            $return['rows'] = $fetch->get();
            $return['keyword'] = 'RaporMutu:0:000000';

            $result = $return;
            // ini adalah akhir prosesnya
            
            Cache::put( "RaporMutu:0:".$key->{'kode_wilayah'}, json_encode($result), 36000);

        }

        return "OK";
    }

    public function rekapRaporMutuWilayah(Request $request){
        $fetch = DB::connection('sqlsrv')
        ->table(DB::raw('ref.wilayah with(nolock)'))
        ->where('level_wilayah_id','=',1);

        $records = $fetch->get();

        foreach ($records as $key) {

            // $opts = array('http' =>
            //     array(
            //         'method'  => 'POST',
            //         'header'  => 'Content-Type: application/x-www-form-urlencoded',
            //         'content' => http_build_query(
            //             array(
            //                 'kode_wilayah' => $key->{'kode_wilayah'},
            //                 'id_level_wilayah' => 1,
            //                 'limit' => 1
            //             )
            //         )
            //     )
            // );
            // $context  = stream_context_create($opts);
            // $result = file_get_contents('http://mpmpapi:8000/api/RaporMutu/RaporMutu', false, $context);

            // ini adalah prosesnya
            $return = array();

            $kolom_kode_wilayah = 'kab.kode_wilayah as kode_wilayah';
            $kolom_nama = 'kab.nama as nama';
            $kolom_induk = 'prop.nama as induk';
            $group_kode_wilayah = 'kab.kode_wilayah';
            $group_nama = 'kab.nama';
            $group_induk = 'prop.nama';
            $param_kode_wilayah = 'prop.kode_wilayah';

            $fetch = DB::connection('sqlsrv')
            ->table(DB::raw('master_pmp with(nolock)'))
            ->join(DB::raw('sekolah with(nolock)'),'sekolah.sekolah_id','=','master_pmp.sekolah_id')
            ->join(DB::raw('ref.wilayah as kec with(nolock)'), 'kec.kode_wilayah', '=', DB::raw('left(sekolah.kode_wilayah,6)'))
            ->join(DB::raw('ref.wilayah as kab with(nolock)'), 'kab.kode_wilayah', '=', 'kec.induk_kode_wilayah')
            ->join(DB::raw('ref.wilayah as prop with(nolock)'), 'prop.kode_wilayah', '=', 'kab.induk_kode_wilayah')
            ->join(DB::raw('ref.wilayah as negara with(nolock)'), 'negara.kode_wilayah', '=', 'prop.induk_kode_wilayah')
            ->select(
                $kolom_kode_wilayah,
                $kolom_nama,
                $kolom_induk,
                DB::raw("AVG ( r16 ) AS r16"),
                DB::raw("AVG ( r17 ) AS r17"),
                DB::raw("AVG ( r18 ) AS r18"),
                DB::raw("AVG ( r19 ) AS r19")
            )
            ->where('level','=','parent')
            ->groupBy($group_kode_wilayah)
            ->groupBy($group_nama)
            ->groupBy($group_induk);
            ;

            $fetch->where($param_kode_wilayah,'=',$key->{'kode_wilayah'});

            $return['total'] = sizeof($fetch->get());
            
            $fetch->skip(0)->take(100);
            
            $return['rows'] = $fetch->get();
            $return['keyword'] = 'RaporMutu:1:'.$key->{'kode_wilayah'};

            $result = $return;
            // ini adalah akhir prosesnya
            
            Cache::put( "RaporMutu:1:".$key->{'kode_wilayah'}, json_encode($result), 36000);

        }

        return "OK";
    }

    public function cek_rekapRaporMutuWilayah(Request $request){
        $fetch = DB::connection('sqlsrv')
        ->table(DB::raw('ref.wilayah with(nolock)'))
        ->where('level_wilayah_id','=',1);

        // return $fetch->get();
        $return_str = '---'.PHP_EOL;

        $records = $fetch->get();

        foreach ($records as $key) {
            // $return_str .= $key->{'nama'};
            $return_str .= PHP_EOL;
            $return_str .= "RaporMutu:1:".$key->{'kode_wilayah'}.PHP_EOL;
            $return_str .= Cache::get( "RaporMutu:1:".$key->{'kode_wilayah'});
            // $return_str .= $result.PHP_EOL.PHP_EOL;
        }

        return $return_str;
    }

    public function cek_rekapRaporMutuNasional(Request $request){
        $fetch = DB::connection('sqlsrv')
        ->table(DB::raw('ref.wilayah with(nolock)'))
        ->where('level_wilayah_id','=',0);

        $return_str = '---'.PHP_EOL;

        $records = $fetch->get();

        foreach ($records as $key) {
            $return_str .= PHP_EOL;
            $return_str .= "RaporMutu:0:".$key->{'kode_wilayah'}.PHP_EOL;
            $return_str .= Cache::get( "RaporMutu:0:".$key->{'kode_wilayah'});
        }

        return $return_str;
    }
}

?>