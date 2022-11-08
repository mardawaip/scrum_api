<?php

namespace App\Http\Controllers;

use App\Models\HasilujiSitu;
use App\Models\LokasiUjiSitu;
use App\Models\LokasiUji;
use App\Models\HasilujiSungai;
use App\Models\LokasiUjiAmbien;
use App\Models\HasilujiAmbien;
use App\Models\LokasiUjiSumur;
use App\Models\HasilujiSumur;
use App\Models\LokasiUjiTanah;
use App\Models\HasilujiTanah;
use App\Models\LokasiUjiCerobong;
use App\Models\HasilujiCerobong;
use App\Models\LokasiUjiLimbahCair;
use App\Models\HasilujiLimbahCair;
use App\Models\Adipura;
use App\Models\Sekolah;
use App\Models\PengawasanIzin;
use App\Models\Pengaduan;
use App\Models\Biogas;
use App\Models\IzinBtiga;
use App\Models\EmisiKdr;

use DB;
use Illuminate\Http\Request;

class RagamDataController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $type, $tahun, $lokasi, $sungai='')
    {
        $request->validate([
            'limit' => 'integer',
            'tahun' => 'integer',
            'lokasi' => 'integer',
            'offset' => 'integer'
        ]);
        
        $limit = $request->limit && $request->limit<=100 ? $request->limit : 25;
        $type = $request->type ? $request->type  : "";
        switch ($type) {
                case 'sungai': return $this->getDataSungai($type, $tahun, $lokasi, $sungai); break;
                case 'situ': return $this->getDataSitu($type, $tahun, $lokasi, $sungai); break;
                    // return HasilujiSitu::with('ParSungaiSitu')
                    // ->where('tahunuji_situ',$request->tahun)
                    // ->where('lokasiuji_situ_id',$request->lokasi)
                    // ->paginate(100);
                // break;
                case 'ambien': return $this->getDataAmbien($type, $tahun, $lokasi, $sungai); break;
                    // return HasilujiAmbien::with('ParAmbien')
                    // ->where('tahunuji_ambien',$request->tahun)
                    // ->where('lokasiuji_ambien_id',$request->lokasi)
                    // ->paginate(100);
                // break;
                case 'limbah_cair':
                    return HasilujiLimbahCair::with('ParLimbahCair')
                    ->where('tahunuji_limbah_cair',$request->tahun)
                    ->where('lokasiuji_limbah_cair_id',$request->lokasi)
                    ->paginate(100);
                break;
                case 'cerobong': return $this->getDataCerobong($type, $tahun, $lokasi, $sungai); break;
                case 'tanah': return $this->getDataTanah($type, $tahun, $lokasi, $sungai); break;
                case 'sumur': return $this->getDataSumur($type, $tahun, $lokasi, $sungai); break;
                case 'mata_air': return $this->getDataMataAir($type, $tahun, $lokasi, $sungai); break;
                case 'sumur_pantau': return $this->getDataSumurPantau($type, $tahun, $lokasi, $sungai); break;
                case 'perusahaan_pengguna_air_tanah': return $this->getDataPerusahaanPenggunaanAirTanah($type, $tahun, $lokasi, $sungai); break;
                case 'sumur_imbuhan': return $this->getSumurImbuhan($type, $tahun, $lokasi, $sungai); break;
                case 'hutan_kota': return $this->getDataHutanKota($type, $tahun, $lokasi, $sungai); break;
                case 'sehati': return $this->getDataSehati($type, $tahun, $lokasi, $sungai); break;
                case 'kehati': return $this->getDataKehati($type, $tahun, $lokasi, $sungai); break;
                case 'pemerhati' : return $this->getDataPemerhati($type, $tahun, $lokasi, $sungai); break;
                case 'sumur_resapan' : return $this->getDataSumurResapan($type, $tahun, $lokasi, $sungai); break;
                case 'realisasi' : return $this->getDataRealisasi($type, $tahun, $lokasi, $sungai); break;
                case 'adipura' : return $this->getDataAdipura($type, $tahun, $lokasi, $sungai); break;
                case 'sekolah_adiwiyata': return $this->getDataSekolahAdiwiyata($type, $tahun, $lokasi, $sungai); break;
                case 'trend_sungai' : return $this->getDataTredSungai($type, $tahun, $lokasi, $sungai); break;
                case 'trend_situ' : return $this->getDataTredSitu($type, $tahun, $lokasi, $sungai); break;
                case 'trend_sumur' : return $this->getDataTredSumur($type, $tahun, $lokasi, $sungai); break;
                case 'trend_ambien' : return $this->getDataTredAmbien($type, $tahun, $lokasi, $sungai); break;
                case 'trend_cerobong' : return $this->getDataTredCerobong($type, $tahun, $lokasi, $sungai); break;
                case 'lingkungan': return $this->getDataLingkungan($type, $tahun, $lokasi, $sungai); break;
                case 'pengaduan': return $this->getDataPengaduan($type, $tahun, $lokasi, $sungai); break;
                case 'pembangunan_biogas' : return $this->getDataPembangunanBiogas($type, $tahun, $lokasi, $sungai); break;
                case 'pengawasan_b3' : return $this->getDataPengawasanB3($type, $tahun, $lokasi, $sungai); break;
                case 'kendaraan' : return $this->getDataKendaraan($type, $tahun, $lokasi, $sungai); break;
                case 'pembangun_limbah_cair' : return $this->getDataPembangunLimbahCair($type, $tahun, $lokasi, $sungai); break;

            //Lokasi UJI
                case 'lokasiujisungai':
                    return array('data'=>LokasiUji::where('sungai_id',$request->lokasi)
                    ->get());
                break;
                case 'lokasiujisitu':
                    return array('data'=>LokasiUjiSitu::where('situ_id',$request->lokasi)
                    ->get());
                break;
                case 'lokasiujiambien':
                    $ambien = DB::select("select lokasiuji_ambien_id as id, nama_lokasi as label from lokasiuji_ambien where lokasiuji_ambien_id in 
                    (SELECT lokasiuji_ambien_id FROM `hasiluji_ambien`
                    where tahunuji_ambien='$request->tahun'
                    group by lokasiuji_ambien_id)");
                    return array('data'=>$ambien);
                    break;
                case 'lokasiujitanah':
                    $tanah = DB::select("select lokasiuji_tanah_id as id, nama_lokasi as label from lokasiuji_tanah where lokasiuji_tanah_id in 
                    (SELECT lokasiuji_tanah_id FROM `hasiluji_tanah`
                    where tahunuji_tanah='$request->tahun'
                    group by lokasiuji_tanah_id)");
                    return array('data'=>$tanah);
                break;
                case 'lokasiujicerobong':
                    $cerobong = DB::select("select lokasiuji_cerobong_id as id, nama_lokasi as label from lokasiuji_cerobong where lokasiuji_cerobong_id in 
                    (SELECT lokasiuji_cerobong_id FROM `hasiluji_cerobong`
                    where tahunuji_cerobong='$request->tahun'
                    group by lokasiuji_cerobong_id)");
                    return array('data'=>$cerobong);
                break;
                case 'lokasiujilimbah_cair':
                    $limbah_cair = DB::select("select lokasiuji_limbah_cair_id as id, nama_lokasi as label from lokasiuji_limbah_cair where lokasiuji_limbah_cair_id in 
                    (SELECT lokasiuji_limbah_cair_id FROM `hasiluji_limbah_cair`
                    where tahunuji_limbah_cair='$request->tahun'
                    group by lokasiuji_limbah_cair_id)");
                    return array('data'=>$limbah_cair);
                break;
                case 'lokasiujisumur':
                    $sumur = DB::select("select lokasiuji_sumur_id as id, nama_lokasi as label from lokasiuji_sumur where lokasiuji_sumur_id in 
                    (SELECT lokasiuji_sumur_id FROM `hasiluji_sumur`
                    where tahunuji_sumur='$request->tahun'
                    group by lokasiuji_sumur_id)");
                    return array('data'=>$sumur);
                break;

                // tahun uji
                case 'tahunuji_sungai':
                    $tahunuji_sungai = DB::select("select tahunuji_sungai as id, tahunuji_sungai as label from tahunuji_sungai order by tahunuji_sungai desc");
                    return array('data'=>$tahunuji_sungai);
                break;
                case 'tahunuji_situ':
                    $tahunuji_situ = DB::select("select tahunuji_situ as id, tahunuji_situ as label from tahunuji_situ order by tahunuji_situ desc");
                    return array('data'=>$tahunuji_situ);
                break;
                case 'tahunuji_limbah_cair':
                    $tahunuji_limbah_cair = DB::select("select tahunuji_limbah_cair as id, tahunuji_limbah_cair as label from tahunuji_limbah_cair order by tahunuji_limbah_cair desc");
                    return array('data'=>$tahunuji_limbah_cair);
                break;
                case 'tahunuji_cerobong':
                    $tahunuji_cerobong = DB::select("select tahunuji_cerobong as id, tahunuji_cerobong as label from tahunuji_cerobong order by tahunuji_cerobong desc");
                    return array('data'=>$tahunuji_cerobong);
                break;
                case 'tahunuji_ambien':
                    $tahunuji_ambien = DB::select("select tahunuji_ambien as id, tahunuji_ambien as label from tahunuji_ambien order by tahunuji_ambien desc");
                    return array('data'=>$tahunuji_ambien);
                break;
                case 'tahunuji_kendaraan':
                    $tahunuji_kendaraan = DB::select("select tahunuji_kendaraan as id, tahunuji_kendaraan as label from tahunuji_kendaraan order by tahunuji_kendaraan desc");
                    return array('data'=>$tahunuji_kendaraan);
                break;
                case 'tahunuji_kendaraan':
                    $tahunuji_kendaraan = DB::select("select tahunuji_kendaraan as id, tahunuji_kendaraan as label from tahunuji_kendaraan order by tahunuji_kendaraan desc");
                    return array('data'=>$tahunuji_kendaraan);
                break;
                case 'tahunuji_tanah':
                    $tahunuji_tanah = DB::select("select tahunuji_tanah as id, tahunuji_tanah as label from tahunuji_tanah order by tahunuji_tanah desc");
                    return array('data'=>$tahunuji_tanah);
                break;
                case 'tahunuji_sumur':
                    $tahunuji_sumur = DB::select("select tahunuji_sumur as id, tahunuji_sumur as label from tahunuji_sumur order by tahunuji_sumur desc");
                    return array('data'=>$tahunuji_sumur);
                break;

                case 'data_tps3r':
                    $filtahun = DB::table('profil')->distinct()->select('tahun_anggaran as value', 'tahun_anggaran as label')->orderby('tahun_anggaran', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $data_tps3r = DB::table('profil')
                        ->select('profil.profil_id AS id','profil.*', 'profil.nama_ksm AS value',  'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
                        ->leftJoin('kelurahan AS klr', 'profil.kelurahan_id', '=', 'klr.kelurahan_id')
                        ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id')
                        ->orderby('profil_id', 'DESC');
                    if($tahun != '-'){ $data_tps3r->where('tahun_anggaran', $sel_tahun); }
                    $rows = $data_tps3r->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;
                 case 'lap_tps3r':
                    $filtahun = DB::table('rekap')->distinct()->select(DB::raw('YEAR(bulan) as value'), DB::raw('YEAR(bulan) as label'))->orderby('bulan', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $lap_tps3r = DB::table('rekap')
                        ->select('rekap.rekap_id AS id', 'rekap.*', 'profil.nama_ksm AS value', 'profil.nama_ksm', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
                        ->join('profil', 'rekap.profil_id', '=', 'profil.profil_id')
                        ->leftJoin('kelurahan AS klr', 'profil.kelurahan_id', '=', 'klr.kelurahan_id')
                        ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id');
                    if($tahun != '-'){ $lap_tps3r->where(DB::raw('YEAR(bulan)'), $sel_tahun); }
                    $rows = $lap_tps3r->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;
                case 'data_sampah':
                    $filtahun = DB::table('terangkut')->distinct()->select(DB::raw('YEAR(tgl) as value'), DB::raw('YEAR(tgl) as label'))->orderby('tgl', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $data_sampah = DB::table('terangkut')->select('terangkut.terangkut_id AS id', 'terangkut.*', 'terangkut.tgl AS value');
                    if($tahun != '-'){ $data_sampah->where(DB::raw('YEAR(tgl)'), $sel_tahun); }
                    $rows = $data_sampah->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;
                case 'data_retribusi':
                    $filtahun = DB::table('retribusi')->distinct()->select('tahun as value', 'tahun as label')->orderby('tahun', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $data_retribusi = DB::table('retribusi')
                        ->select('retribusi.retribusi_id AS id', 'retribusi.*', 'retribusi.nama AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
                        ->leftJoin('kelurahan AS klr', 'retribusi.kelurahan_id', '=', 'klr.kelurahan_id')
                        ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id');
                    if($tahun != '-'){ $data_retribusi->where('tahun', $sel_tahun); }
                    $rows = $data_retribusi->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;
                case 'data_basiba':
                    $filtahun = DB::table('basiba')->distinct()->select('tahun as value', 'tahun as label')->orderby('tahun', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $data_basiba = DB::table('basiba')
                        ->select('basiba.basiba_id AS id', 'basiba.*', 'basiba.nama AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
                        ->leftJoin('kelurahan AS klr', 'basiba.kelurahan_id', '=', 'klr.kelurahan_id')
                        ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id')
                        ->orderby('basiba_id', 'DESC');
                    if($tahun != '-'){ $data_basiba->where('tahun', $sel_tahun); }
                    $rows = $data_basiba->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;

                //Dampak Lingkungan
                case 'sppl':
                    $filtahun = DB::table('sppl_lampau')->distinct()->select('tanggal as value', 'tanggal as label')->orderby('tanggal', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $sppl = DB::table('sppl_lampau')->select('sppl_lampau.*', 'sppl_lampau.nama_kegiatan AS value');
                    if($tahun != '-'){ $sppl->where('tanggal', $sel_tahun); }
                    $rows = $sppl->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;
                case 'ukl_upl':
                    $filtahun = DB::table('ukl_upl_lampau')->distinct()->select('tanggal as value', 'tanggal as label')->orderby('tanggal', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $ukl_upl = DB::table('ukl_upl_lampau')->select('ukl_upl_lampau.*', 'ukl_upl_lampau.nama_kegiatan AS value');
                    if($tahun != '-'){ $ukl_upl->where('tanggal', $sel_tahun); }
                    $rows = $ukl_upl->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;
                case 'amdal':
                    $filtahun = DB::table('amdal_lampau')->distinct()->select('tanggal_ka_andal as value', 'tanggal_ka_andal as label')->orderby('tanggal_ka_andal', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $amdal = DB::table('amdal_lampau')->select('amdal_lampau.*', 'amdal_lampau.nama_kegiatan AS value');
                    if($tahun != '-'){ $amdal->where('tanggal_ka_andal', $sel_tahun); }
                    $rows = $amdal->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;
                case 'izin':
                    $filtahun = DB::table('izin_lingkungan')->distinct()->select('tahun as value', 'tahun as label')->orderby('tahun', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $izin = DB::table('izin_lingkungan')->select(DB::raw("izin_lingkungan.izin_lingkungan_id As id"), 'izin_lingkungan.*', 'izin_lingkungan.kegiatan AS value');
                    if($tahun != '-'){ $izin->where('tahun', $sel_tahun); }
                    $rows = $izin->get();

                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;

                // Penerimaan Penghargaan
                case 'penghargaan':
                    $filtahun = DB::table('penerimaan_penghargaan')->distinct()->select('tahun as value', 'tahun as label')->orderby('tahun', 'DESC')->get();
                    $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
                    $penghargaan = DB::table('penerimaan_penghargaan')->select(DB::raw("penerimaan_penghargaan.penerimaan_penghargaan_id As id"), 'penerimaan_penghargaan.*', 'penerimaan_penghargaan.nama_penghargaan AS value');
                    if($tahun != '-'){ $penghargaan->where('tahun', $sel_tahun); }
                    $rows = $penghargaan->get();
                    
                    return array(
                        'data' => $rows,
                        'select' => [
                            'tahun' => $filtahun
                        ],
                        'selected' => [
                            'tahun' => $sel_tahun
                        ]
                    );
                break;
            default:
                return ['data' => []];
                    break;
        }

    }

    public function getHasilUjiGroup($tabel, $kolom)
    {
        $db = DB::table($tabel)->select("$kolom AS tahun", DB::raw("SUM(1) AS jml"))->groupBy($kolom)->get();

        $data = [];
        foreach ($db as $key) {
            $data[$key->tahun] = $key->jml;
        }

        return $data;
    }

    public function getGrafikTime(Request $request)
    {
        $tahun = ['2010', '2011', '2012', '2013', '2014', '2015', '2016', '2017', '2018', '2019', '2020', '2021'];
        $label = ['Hasiluji Ambien', 'Hasiluji Cerobong', 'Hasiluji Kendaraan', 'Hasiluji Limbah Cair', 'Hasiluji Situ', 'Hasiluji Sumur', 'Hasiluji Sungai', 'Hasiluji Tanah'];
        $tabel = ['hasiluji_ambien', 'hasiluji_cerobong', 'hasiluji_kendaraan', 'hasiluji_limbah_cair', 'hasiluji_situ', 'hasiluji_sumur', 'hasiluji_sungai', 'hasiluji_tanah'];
        $kolom = ['tahunuji_ambien', 'tahunuji_cerobong', 'tahunuji_kendaraan', 'tahunuji_limbah_cair', 'tahunuji_situ', 'tahunuji_sumur', 'tahunuji_sungai', 'tahunuji_tanah'];

        for ($i=0; $i < count($tabel) ; $i++) { 
            $db = $this->getHasilUjiGroup($tabel[$i], $kolom[$i]);

            $nilai = [];
            for ($a=0; $a < count($tahun) ; $a++) { 
                $nilai[$a] = @$db[$tahun[$a]] ? @$db[$tahun[$a]] : 0;
            }

            $datas[$i] = [
                'data' => $nilai,
                'name' => $label[$i],
                'type' => "line"
            ];
        }

        return [
            'data' => [
                'datas' => $datas,
                'labels' => $tahun
            ]
        ];
    }

    public function getArrayFiltered($aFilterKey, $aFilterValue, $array) {
        $filtered_array = array();
        foreach ($array as $value) {
            if (isset($value->$aFilterKey)) {
                if ($value->$aFilterKey == $aFilterValue) {
                    $filtered_array[] = $value;
                }
            }
        }

        return $filtered_array;
    }

    public function getDataSungai($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_sungai')->distinct()->select('tahunuji_sungai as value', 'tahunuji_sungai as label')->orderby('tahunuji_sungai', 'DESC')->get();
        $filsungai = DB::table('sungai')->select('sungai_id AS value', 'nama_sungai AS label')->orderby('nama_sungai', 'ASC')->get();
        $fillokasi = DB::table('hasiluji_sungai AS hs')
            ->distinct()
            ->select(
                'ls.lokasiuji_sungai_id AS value',
                'ls.nama_lokasi AS label',
                'hs.tahunuji_sungai AS tahun',
                'ls.sungai_id',
            )
            ->leftJoin('lokasiuji_sungai AS ls', 'hs.lokasiuji_sungai_id', '=', 'ls.lokasiuji_sungai_id')
            ->get();

        $last = HasilujiSungai::orderby('hasil_uji_sungai_id', 'DESC')->first();

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahunuji_sungai;
        $sel_sungai = $sungai != "na" ? $sungai : $last->sungai_id;
        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->lokasiuji_sungai_id;

        $db = HasilujiSungai::orderby('hasil_uji_sungai_id', 'DESC');

        if($tahun != '-'){ $db->where('tahunuji_sungai', $sel_tahun); }
        if($lokasi != '-'){ $db->where('lokasiuji_sungai_id', $sel_lokasi); }
        // if($sungai != '-'){ $db->where('sungai_id', $sel_sungai); }

        $rows = $db->get();

        $data1 = $this->getArrayFiltered('periode', 0, $rows);
        $data2 = $this->getArrayFiltered('periode', 1, $rows);

        $count = count($data1) > count($data2) ? count($data1) : count($data2);

        $rows = [];
        for ($i=0; $i < $count ; $i++) {
            $rows[$i]['id'] = $i;
            $rows[$i]['value'] = @$data1[$i]->parameter.'-'.@$data2[$i]->parameter;
            ;
            $rows[$i]['parameter'] = @$data1[$i]->parameter;
            $rows[$i]['baku_mutu'] = @$data1[$i]->baku_mutu;
            $rows[$i]['hasil_uji'] = @$data1[$i]->hasil_uji;
            $rows[$i]['tandabaca'] = @$data1[$i]->tandabaca;
            $rows[$i]['satuan']    = @$data1[$i]->satuan;
            $rows[$i]['ket_akhir'] = @$data1[$i]->ket_akhir;

            $rows[$i]['parameter2'] = @$data2[$i]->parameter;
            $rows[$i]['baku_mutu2'] = @$data2[$i]->baku_mutu;
            $rows[$i]['hasil_uji2'] = @$data2[$i]->hasil_uji;
            $rows[$i]['tandabaca2'] = @$data2[$i]->tandabaca;
            $rows[$i]['satuan2']    = @$data2[$i]->satuan;
            $rows[$i]['ket_akhir2'] = @$data2[$i]->ket_akhir;
        }

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
                'sungai' => $filsungai,
                'lokasi' => $fillokasi
            ],
            'selected' => [
                'tahun' => $sel_tahun,
                'sungai' => $sel_sungai,
                'lokasi' => $sel_lokasi
            ]
        );
    }

    public function getDataSitu($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_situ')->distinct()->select('tahunuji_situ as value', 'tahunuji_situ as label')->orderby('tahunuji_situ', 'DESC')->get();
        $filsungai = DB::table('situ')->select('situ_id AS value', 'nama_situ AS label')->orderby('nama_situ', 'ASC')->get();
        $fillokasi = DB::table('hasiluji_situ AS hs')
            ->distinct()
            ->select(
                'ls.lokasiuji_situ_id AS value',
                'ls.nama_lokasi AS label',
                'hs.tahunuji_situ AS tahun',
                'ls.situ_id AS sungai_id',
            )
            ->leftJoin('lokasiuji_situ AS ls', 'hs.lokasiuji_situ_id', '=', 'ls.lokasiuji_situ_id')
            ->get();

        $last = HasilujiSitu::orderby('hasil_uji_situ_id', 'DESC')->first();

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahunuji_situ;
        $sel_sungai = $sungai != "na" ? $sungai : $last->situ_id;
        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->lokasiuji_situ_id;

        $db = HasilujiSitu::orderby('hasil_uji_situ_id', 'DESC');

        if($tahun != '-'){ $db->where('tahunuji_situ', $sel_tahun); }
        if($lokasi != '-'){ $db->where('lokasiuji_situ_id', $sel_lokasi); }
        // if($sungai != '-'){ $db->where('sungai_id', $sel_sungai); }

        $rows = $db->get();

        $data1 = $this->getArrayFiltered('periode', 0, $rows);
        $data2 = $this->getArrayFiltered('periode', 1, $rows);

        $count = count($data1) > count($data2) ? count($data1) : count($data2);

        $rows = [];
        for ($i=0; $i < $count ; $i++) {
            $rows[$i]['id'] = $i;
            $rows[$i]['value'] = @$data1[$i]->parameter.'-'.@$data2[$i]->parameter;
            ;
            $rows[$i]['parameter'] = @$data1[$i]->parameter;
            $rows[$i]['baku_mutu'] = @$data1[$i]->baku_mutu;
            $rows[$i]['hasil_uji'] = @$data1[$i]->hasil_uji;
            $rows[$i]['tandabaca'] = @$data1[$i]->tandabaca;
            $rows[$i]['satuan']    = @$data1[$i]->satuan;
            $rows[$i]['ket_akhir'] = @$data1[$i]->ket_akhir;

            $rows[$i]['parameter2'] = @$data2[$i]->parameter;
            $rows[$i]['baku_mutu2'] = @$data2[$i]->baku_mutu;
            $rows[$i]['hasil_uji2'] = @$data2[$i]->hasil_uji;
            $rows[$i]['tandabaca2'] = @$data2[$i]->tandabaca;
            $rows[$i]['satuan2']    = @$data2[$i]->satuan;
            $rows[$i]['ket_akhir2'] = @$data2[$i]->ket_akhir;
        }

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
                'sungai' => $filsungai,
                'lokasi' => $fillokasi
            ],
            'selected' => [
                'tahun' => $sel_tahun,
                'sungai' => $sel_sungai,
                'lokasi' => $sel_lokasi,
                'last' => $last
            ]
        );
    }

    public function getDataAmbien($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_ambien')->distinct()->select('tahunuji_ambien as value', 'tahunuji_ambien as label')->orderby('tahunuji_ambien', 'DESC')->get();
        $fillokasi = DB::table('hasiluji_ambien AS hs')->distinct()->select(
            'ls.lokasiuji_ambien_id AS value',
            'ls.nama_lokasi AS label',
            'hs.tahunuji_ambien AS tahun'
            )
            ->leftJoin('lokasiuji_ambien AS ls', 'hs.lokasiuji_ambien_id', '=', 'ls.lokasiuji_ambien_id')
            ->get();

        $last = HasilujiAmbien::orderby('hasiluji_ambien_id', 'DESC')->first();

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahunuji_ambien;
        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->lokasiuji_ambien_id;
        
        $ambien = HasilujiAmbien::orderby('hasiluji_ambien_id', 'ASC');

        if($tahun != '-'){ $ambien->where('tahunuji_ambien', $sel_tahun); }
        if($lokasi != '-'){ $ambien->where('lokasiuji_ambien_id', $sel_lokasi); }

        $rows = $ambien->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
                'lokasi' => $fillokasi
            ],
            'selected' => [
                'tahun' => $sel_tahun,
                'lokasi' => $sel_lokasi
            ]
        );
    }

    public function getDataCerobong($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_cerobong')->distinct()->select('tahunuji_cerobong as value', 'tahunuji_cerobong as label')->orderby('tahunuji_cerobong', 'DESC')->get();
        $fillokasi = DB::table('hasiluji_cerobong AS hs')->distinct()->select(
            'ls.lokasiuji_cerobong_id AS value',
            'ls.nama_lokasi AS label',
            'hs.tahunuji_cerobong AS tahun'
            )
            ->leftJoin('lokasiuji_cerobong AS ls', 'hs.lokasiuji_cerobong_id', '=', 'ls.lokasiuji_cerobong_id')
            ->get();

        $last = HasilujiCerobong::orderby('id', 'DESC')->first();

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahunuji_cerobong;
        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->lokasiuji_cerobong_id;
        
        $ambien = HasilujiCerobong::orderby('id', 'ASC');

        if($tahun != '-'){ $ambien->where('tahunuji_cerobong', $sel_tahun); }
        if($lokasi != '-'){ $ambien->where('lokasiuji_cerobong_id', $sel_lokasi); }

        $rows = $ambien->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
                'lokasi' => $fillokasi
            ],
            'selected' => [
                'tahun' => $sel_tahun,
                'lokasi' => $sel_lokasi
            ]
        );
    }

    public function getDataTanah($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_tanah')->distinct()->select('tahunuji_tanah as value', 'tahunuji_tanah as label')->orderby('tahunuji_tanah', 'DESC')->get();
        $fillokasi = DB::table('hasiluji_tanah AS hs')->distinct()->select(
            'ls.lokasiuji_tanah_id AS value',
            'ls.nama_lokasi AS label',
            'hs.tahunuji_tanah AS tahun'
            )
            ->leftJoin('lokasiuji_tanah AS ls', 'hs.lokasiuji_tanah_id', '=', 'ls.lokasiuji_tanah_id')
            ->get();

        $last = HasilujiTanah::orderby('id', 'DESC')->first();

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahunuji_tanah;
        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->lokasiuji_tanah_id;
        
        $ambien = HasilujiTanah::orderby('id', 'ASC');

        if($tahun != '-'){ $ambien->where('tahunuji_tanah', $sel_tahun); }
        if($lokasi != '-'){ $ambien->where('lokasiuji_tanah_id', $sel_lokasi); }

        $rows = $ambien->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
                'lokasi' => $fillokasi
            ],
            'selected' => [
                'tahun' => $sel_tahun,
                'lokasi' => $sel_lokasi
            ]
        );
    }

    public function getDataSumur($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_sumur')->distinct()->select('tahunuji_sumur as value', 'tahunuji_sumur as label')->orderby('tahunuji_sumur', 'DESC')->get();
        $fillokasi = DB::table('hasiluji_sumur AS hs')->distinct()->select(
            'ls.lokasiuji_sumur_id AS value',
            'ls.nama_lokasi AS label',
            'hs.tahunuji_sumur AS tahun'
            )
            ->leftJoin('lokasiuji_sumur AS ls', 'hs.lokasiuji_sumur_id', '=', 'ls.lokasiuji_sumur_id')
            ->get();

        $last = HasilujiSumur::orderby('id', 'DESC')->first();

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahunuji_sumur;
        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->lokasiuji_sumur_id;
        
        $sumur = HasilujiSumur::orderby('id', 'DESC');

        if($tahun != '-'){ $sumur->where('tahunuji_sumur', $sel_tahun); }
        if($lokasi != '-'){ $sumur->where('lokasiuji_sumur_id', $sel_lokasi); }
        
        $rows = $sumur->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
                'lokasi' => $fillokasi
            ],
            'selected' => [
                'tahun' => $sel_tahun,
                'lokasi' => $sel_lokasi
            ]
        );
    }

    public function getDataMataAir($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('mata_air')->distinct()->select('tahun_pengamatan as value', 'tahun_pengamatan as label')->orderby('tahun_pengamatan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('mata_air')
            ->select('mata_air.mata_air_id AS id','mata_air.*', 'klr.kelurahan_nama AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'mata_air.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id');
        if($tahun != '-'){ $db->where('tahun_pengamatan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataSumurPantau($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('sumur_pantau')->distinct()->select('tahun_pengamatan as value', 'tahun_pengamatan as label')->orderby('tahun_pengamatan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('sumur_pantau')
            ->select('sumur_pantau.sumur_pantau_id AS id','sumur_pantau.*', 'klr.kelurahan_nama AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'sumur_pantau.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id');
        if($tahun != '-'){ $db->where('tahun_pengamatan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataPerusahaanPenggunaanAirTanah($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('pemanfaatan_air')->distinct()->select('tahun_pengawasan as value', 'tahun_pengawasan as label')->orderby('tahun_pengawasan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('pemanfaatan_air')
            ->select('pemanfaatan_air.pemanfaatan_air_id AS id','pemanfaatan_air.*', 'pemanfaatan_air.nama_kepemilikan AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'pemanfaatan_air.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id')
            ->orderby('pemanfaatan_air.nama_kepemilikan', 'ASC');
        if($tahun != '-'){ $db->where('tahun_pengawasan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getSumurImbuhan($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('sumur_imbuhan')->distinct()->select('tahun_pengamatan as value', 'tahun_pengamatan as label')->orderby('tahun_pengamatan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('sumur_imbuhan')
            ->select('sumur_imbuhan.sumur_imbuhan_id AS id','sumur_imbuhan.*', 'klr.kelurahan_nama AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'sumur_imbuhan.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id');
        if($tahun != '-'){ $db->where('tahun_pengamatan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataHutanKota($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hutan_kota')->distinct()->select('tahun_pendataan as value', 'tahun_pendataan as label')->orderby('tahun_pendataan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('hutan_kota')
            ->select('hutan_kota.hutan_kota_id AS id','hutan_kota.*', 'hutan_kota.nama_lokasi AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'hutan_kota.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id');
        if($tahun != '-'){ $db->where('tahun_pendataan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataSehati($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('skp')->distinct()->select('tahun_pendataan as value', 'tahun_pendataan as label')->orderby('tahun_pendataan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('skp')
            ->select('skp.skp_id AS id','skp.*', 'skp.nama_lokasi AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'skp.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id')
            ->where('skp.kategori', 'Sehati');
        if($tahun != '-'){ $db->where('tahun_pendataan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataKehati($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('skp')->distinct()->select('tahun_pendataan as value', 'tahun_pendataan as label')->orderby('tahun_pendataan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('skp')
            ->select('skp.skp_id AS id','skp.*', 'skp.nama_lokasi AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'skp.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id')
            ->where('skp.kategori', 'Kehati');
        if($tahun != '-'){ $db->where('tahun_pendataan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataPemerhati($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('skp')->distinct()->select('tahun_pendataan as value', 'tahun_pendataan as label')->orderby('tahun_pendataan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('skp')
            ->select('skp.skp_id AS id','skp.*', 'skp.nama_lokasi AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'skp.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id')
            ->where('skp.kategori', 'Pemerhati');
        if($tahun != '-'){ $db->where('tahun_pendataan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataSumurResapan($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('sumur_resapan')->distinct()->select('tahun_pengamatan as value', 'tahun_pengamatan as label')->orderby('tahun_pengamatan', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('sumur_resapan')
            ->select('sumur_resapan.sumur_resapan_id AS id','sumur_resapan.*', 'klr.kelurahan_nama AS value', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'sumur_resapan.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id');
        if($tahun != '-'){ $db->where('tahun_pengamatan', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataRealisasi($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('realisasi_kegiatan')->distinct()->select('tahun as value', 'tahun as label')->orderby('tahun', 'DESC')->get();
        $sel_tahun = $tahun != "na" ? $tahun : ($filtahun[0] ? $filtahun[0]->value : '');
        $db = DB::table('realisasi_kegiatan')
            ->select('realisasi_kegiatan.realisasi_kegiatan_id AS id','realisasi_kegiatan.*', 'kec.kecamatan_nama AS value', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kecamatan AS kec', 'realisasi_kegiatan.kecamatan_id', '=', 'kec.kecamatan_id');
        if($tahun != '-'){ $db->where('tahun', $sel_tahun); }
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun
            ],
            'selected' => [
                'tahun' => $sel_tahun
            ]
        );
    }

    public function getDataAdipura($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('adipura')->distinct()->select('tahun_pengamatan as value', 'tahun_pengamatan as label')->orderby('tahun_pengamatan', 'DESC')->get();
        $fillokasi = DB::table('adipura')->distinct()->select('komponen as value', 'komponen as label')->orderby('komponen', 'DESC')->get();
        $last = Adipura::orderby('adipura_id', 'DESC')->first();

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahun_pengamatan;
        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->komponen;

        $db = DB::table('adipura')
            ->select('adipura.adipura_id AS id','adipura.*', 'klr.kelurahan_nama AS kelurahan', 'kec.kecamatan_nama AS kecamatan')
            ->leftJoin('kelurahan AS klr', 'adipura.kelurahan_id', '=', 'klr.kelurahan_id')
            ->leftJoin('kecamatan AS kec', 'klr.kecamatan_id', '=', 'kec.kecamatan_id');
        
        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
                'lokasi' => $fillokasi
            ],
            'selected' => [
                'tahun' => $sel_tahun,
                'lokasi' => $sel_lokasi
            ]
        );
    }

    public function getDataSekolahAdiwiyata($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('sekolah')->distinct()->select('tahun_penghargaan as value', 'tahun_penghargaan as label')->orderby('tahun_penghargaan', 'DESC')->get();
        $last = Sekolah::orderby('sekolah_id', 'DESC')->first();

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahun_penghargaan;

        $sekolah = Sekolah::orderby('sekolah_id', 'DESC');

        if($tahun != '-'){ $sekolah->where('tahun_penghargaan', $sel_tahun); }

        $rows = $sekolah->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
            ],
            'selected' => [
                'tahun' => $sel_tahun,
            ]
        );
    }

    public function getDataTredSungai($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_sungai')->distinct()->select('tahunuji_sungai AS value', 'tahunuji_sungai AS label')->orderby('tahunuji_sungai', 'DESC')->get();
        $fillokasi = DB::table('lokasiuji_sungai')->distinct()->select('lokasiuji_sungai_id AS value', 'nama_lokasi AS label')->orderby('nama_lokasi', 'ASC')->get();
        $filparameter = DB::table('par_sungai_situ')->distinct()->select('par_sungai_situ_id AS value', 'parameter AS label')->orderby("parameter",'ASC')->get();

        $last_tahun = $filtahun[(count($filtahun)-1)]->value."-".$filtahun[0]->value;

        $sel_lokasi = $lokasi != "na" ? (int)$lokasi : ($fillokasi[0] ? $fillokasi[0]->value : '');
        $sel_sungai = $sungai != "na" ? (int)$sungai : ($filparameter[0] ? $filparameter[0]->value : '');

        $distahun = DB::table('hasiluji_sungai')->distinct()->select('tahunuji_sungai AS tahun')->orderby('tahunuji_sungai', 'ASC')->get();

        $data0 = [];
        $data1 = [];
        $data2 = [];

        $i = 0;
        foreach ($distahun as $key) {
            $thistahun = $key->tahun ? $key->tahun : '0';
            $db = DB::table('hasiluji_sungai')->distinct()->where('lokasiuji_sungai_id', $sel_lokasi)->where('par_sungai_situ_id', $sel_sungai)->where('tahunuji_sungai', $thistahun)->get();

            $periode0 = $this->getArrayFiltered('periode', 0, $db);
            $periode1 = $this->getArrayFiltered('periode', 1, $db);

            $data0[$i] = @$periode0[0]->hasil_uji ? (float)str_replace(",", ".", $periode0[0]->hasil_uji) : 0;
            $data1[$i] = @$periode1[0]->hasil_uji ? (float)str_replace(",", ".", $periode1[0]->hasil_uji) : 0;

            $labes[$i][0] = $thistahun;
            $labes[$i][1] = "( ".(@$periode0[0]->baku_mutu ? $periode0[0]->baku_mutu : 0)." )";

            $i++;
        }

        $periode[0] = ['type' => 'column', 'name' => 'Musim Kemarau', 'data' => $data0];
        $periode[1] = ['type' => 'column', 'name' => 'Musim Penghujan', 'data' => $data1];

        $chart = [
            'labels' => $labes,
            'datas' => $periode
        ];

        return array(
            'data' => $chart,
            'select' => [
                'tahunTo' => $filtahun,
                'lokasi' => $fillokasi,
                'parameter' => $filparameter
            ],
            'selected' => [
                'tahun' => $last_tahun,
                'sungai' => $sel_sungai, // parameter
                'lokasi' => $sel_lokasi,
            ]
        );
    }

    public function getDataTredSitu($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_situ')->distinct()->select('tahunuji_situ AS value', 'tahunuji_situ AS label')->orderby('tahunuji_situ', 'DESC')->get();
        $fillokasi = DB::table('lokasiuji_situ')->distinct()->select('lokasiuji_situ_id AS value', 'nama_lokasi AS label')->orderby('nama_lokasi', 'ASC')->get();
        $filparameter = DB::table('par_sungai_situ')->distinct()->select('par_sungai_situ_id AS value', 'parameter AS label')->orderby("parameter",'ASC')->get();

        $last_tahun = $filtahun[(count($filtahun)-1)]->value."-".$filtahun[0]->value;

        $sel_lokasi = $lokasi != "na" ? (int)$lokasi : ($fillokasi[0] ? $fillokasi[0]->value : '');
        $sel_sungai = $sungai != "na" ? (int)$sungai : ($filparameter[0] ? $filparameter[0]->value : '');

        $distahun = DB::table('hasiluji_situ')->distinct()->select('tahunuji_situ AS tahun')->orderby('tahunuji_situ', 'ASC')->get();

        $data0 = [];
        $data1 = [];
        $data2 = [];

        $i = 0;
        foreach ($distahun as $key) {
            $thistahun = $key->tahun ? $key->tahun : '0';
            $db = DB::table('hasiluji_situ')->distinct()->where('lokasiuji_situ_id', $sel_lokasi)->where('par_sungai_situ_id', $sel_sungai)->where('tahunuji_situ', $thistahun)->get();

            $periode0 = $this->getArrayFiltered('periode', 0, $db);
            $periode1 = $this->getArrayFiltered('periode', 1, $db);

            $data0[$i] = @$periode0[0]->hasil_uji ? (float)str_replace(",", ".", $periode0[0]->hasil_uji) : 0;
            $data1[$i] = @$periode1[0]->hasil_uji ? (float)str_replace(",", ".", $periode1[0]->hasil_uji) : 0;

            $labes[$i][0] = $thistahun;
            $labes[$i][1] = "( ".(@$periode0[0]->baku_mutu ? $periode0[0]->baku_mutu : 0)." )";

            $i++;
        }

        $periode[0] = ['type' => 'column', 'name' => 'Musim Kemarau', 'data' => $data0];
        $periode[1] = ['type' => 'column', 'name' => 'Musim Penghujan', 'data' => $data1];

        $chart = [
            'labels' => $labes,
            'datas' => $periode
        ];

        return array(
            'data' => $chart,
            'select' => [
                'tahunTo' => $filtahun,
                'lokasi' => $fillokasi,
                'parameter' => $filparameter
            ],
            'selected' => [
                'tahun' => $last_tahun,
                'sungai' => $sel_sungai, // parameter
                'lokasi' => $sel_lokasi,
            ]
        );
    }

    public function getDataTredSumur($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_sumur')->distinct()->select('tahunuji_sumur AS value', 'tahunuji_sumur AS label')->orderby('tahunuji_sumur', 'DESC')->get();
        $fillokasi = DB::table('lokasiuji_sumur')->distinct()->select('lokasiuji_sumur_id AS value', 'nama_lokasi AS label')->orderby('nama_lokasi', 'ASC')->get();
        $filparameter = DB::table('par_sumur')->distinct()->select('par_sumur_id AS value', 'parameter AS label')->orderby("parameter",'ASC')->get();

        $last_tahun = $filtahun[(count($filtahun)-1)]->value."-".$filtahun[0]->value;

        $sel_lokasi = $lokasi != "na" ? (int)$lokasi : ($fillokasi[0] ? $fillokasi[0]->value : '');
        $sel_sungai = $sungai != "na" ? (int)$sungai : ($filparameter[0] ? $filparameter[0]->value : '');

        $distahun = DB::table('hasiluji_sumur')->distinct()->select('tahunuji_sumur AS tahun')->orderby('tahunuji_sumur', 'ASC')->get();

        $data0 = [];
        $data1 = [];
        $data2 = [];

        $i = 0;
        foreach ($distahun as $key) {
            $thistahun = $key->tahun ? $key->tahun : '0';
            $db = DB::table('hasiluji_sumur')->distinct()->where('lokasiuji_sumur_id', $sel_lokasi)->where('par_sumur_id', $sel_sungai)->where('tahunuji_sumur', $thistahun)->get();

            $periode0 = $this->getArrayFiltered('periode', 0, $db);
            $periode1 = $this->getArrayFiltered('periode', 1, $db);

            $data0[$i] = @$periode0[0]->hasil_uji ? (float)str_replace(",", ".", $periode0[0]->hasil_uji) : 0;
            $data1[$i] = @$periode1[0]->hasil_uji ? (float)str_replace(",", ".", $periode1[0]->hasil_uji) : 0;

            $labes[$i][0] = $thistahun;
            $labes[$i][1] = "( ".(@$periode0[0]->baku_mutu ? $periode0[0]->baku_mutu : 0)." )";

            $i++;
        }

        $periode[0] = ['type' => 'column', 'name' => 'Musim Kemarau', 'data' => $data0];
        $periode[1] = ['type' => 'column', 'name' => 'Musim Penghujan', 'data' => $data1];

        $chart = [
            'labels' => $labes,
            'datas' => $periode
        ];

        return array(
            'data' => $chart,
            'select' => [
                'tahunTo' => $filtahun,
                'lokasi' => $fillokasi,
                'parameter' => $filparameter
            ],
            'selected' => [
                'tahun' => $last_tahun,
                'sungai' => $sel_sungai, // parameter
                'lokasi' => $sel_lokasi,
            ]
        );
    }

    public function getDataTredAmbien($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_ambien')->distinct()->select('tahunuji_ambien AS value', 'tahunuji_ambien AS label')->orderby('tahunuji_ambien', 'DESC')->get();
        $fillokasi = DB::table('lokasiuji_ambien')->distinct()->select('lokasiuji_ambien_id AS value', 'nama_lokasi AS label')->orderby('nama_lokasi', 'ASC')->get();
        $filparameter = DB::table('par_ambien')->distinct()->select('par_ambien_id AS value', 'parameter AS label')->orderby("parameter",'ASC')->get();

        $last_tahun = $filtahun[(count($filtahun)-1)]->value."-".$filtahun[0]->value;

        $sel_lokasi = $lokasi != "na" ? (int)$lokasi : ($fillokasi[0] ? $fillokasi[0]->value : '');
        $sel_sungai = $sungai != "na" ? (int)$sungai : ($filparameter[0] ? $filparameter[0]->value : '');

        $distahun = DB::table('hasiluji_ambien')->distinct()->select('tahunuji_ambien AS tahun')->orderby('tahunuji_ambien', 'ASC')->get();

        $data0 = [];
        $data1 = [];
        $data2 = [];

        $i = 0;
        foreach ($distahun as $key) {
            $thistahun = $key->tahun ? $key->tahun : '0';
            $db = DB::table('hasiluji_ambien')->distinct()->where('lokasiuji_ambien_id', $sel_lokasi)->where('par_ambien_id', $sel_sungai)->where('tahunuji_ambien', $thistahun)->get();

            $periode0 = $this->getArrayFiltered('periode', 0, $db);
            $periode1 = $this->getArrayFiltered('periode', 1, $db);

            $data0[$i] = @$periode0[0]->hasil_uji ? (float)str_replace(",", ".", $periode0[0]->hasil_uji) : 0;
            $data1[$i] = @$periode1[0]->hasil_uji ? (float)str_replace(",", ".", $periode1[0]->hasil_uji) : 0;

            $labes[$i][0] = $thistahun;
            $labes[$i][1] = "( ".(@$periode0[0]->baku_mutu ? $periode0[0]->baku_mutu : 0)." )";

            $i++;
        }

        $periode[0] = ['type' => 'column', 'name' => 'Musim Kemarau', 'data' => $data0];
        $periode[1] = ['type' => 'column', 'name' => 'Musim Penghujan', 'data' => $data1];

        $chart = [
            'labels' => $labes,
            'datas' => $periode
        ];

        return array(
            'data' => $chart,
            'select' => [
                'tahunTo' => $filtahun,
                'lokasi' => $fillokasi,
                'parameter' => $filparameter
            ],
            'selected' => [
                'tahun' => $last_tahun,
                'sungai' => $sel_sungai, // parameter
                'lokasi' => $sel_lokasi,
            ]
        );
    }

    public function getDataTredCerobong($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_cerobong')->distinct()->select('tahunuji_cerobong AS value', 'tahunuji_cerobong AS label')->orderby('tahunuji_cerobong', 'DESC')->get();
        $fillokasi = DB::table('lokasiuji_cerobong')->distinct()->select('lokasiuji_cerobong_id AS value', 'nama_lokasi AS label')->orderby('nama_lokasi', 'ASC')->get();
        $filparameter = DB::table('par_cerobong')->distinct()->select('par_cerobong_id AS value', 'parameter AS label')->orderby("parameter",'ASC')->get();

        $last_tahun = $filtahun[(count($filtahun)-1)]->value."-".$filtahun[0]->value;

        $sel_lokasi = $lokasi != "na" ? (int)$lokasi : ($fillokasi[0] ? $fillokasi[0]->value : '');
        $sel_sungai = $sungai != "na" ? (int)$sungai : ($filparameter[0] ? $filparameter[0]->value : '');

        $distahun = DB::table('hasiluji_cerobong')->distinct()->select('tahunuji_cerobong AS tahun')->orderby('tahunuji_cerobong', 'ASC')->get();

        $data0 = [];
        $data1 = [];
        $data2 = [];

        $i = 0;
        foreach ($distahun as $key) {
            $thistahun = $key->tahun ? $key->tahun : '0';
            $db = DB::table('hasiluji_cerobong')->distinct()->where('lokasiuji_cerobong_id', $sel_lokasi)->where('par_cerobong_id', $sel_sungai)->where('tahunuji_cerobong', $thistahun)->get();

            $periode0 = $this->getArrayFiltered('periode', 0, $db);
            $periode1 = $this->getArrayFiltered('periode', 1, $db);

            $data0[$i] = @$periode0[0]->hasil_uji ? (float)str_replace(",", ".", $periode0[0]->hasil_uji) : 0;
            $data1[$i] = @$periode1[0]->hasil_uji ? (float)str_replace(",", ".", $periode1[0]->hasil_uji) : 0;

            $labes[$i][0] = $thistahun;
            $labes[$i][1] = "( ".(@$periode0[0]->baku_mutu ? $periode0[0]->baku_mutu : 0)." )";

            $i++;
        }

        $periode[0] = ['type' => 'column', 'name' => 'Musim Kemarau', 'data' => $data0];
        $periode[1] = ['type' => 'column', 'name' => 'Musim Penghujan', 'data' => $data1];

        $chart = [
            'labels' => $labes,
            'datas' => $periode
        ];

        return array(
            'data' => $chart,
            'select' => [
                'tahunTo' => $filtahun,
                'lokasi' => $fillokasi,
                'parameter' => $filparameter
            ],
            'selected' => [
                'tahun' => $last_tahun,
                'sungai' => $sel_sungai, // parameter
                'lokasi' => $sel_lokasi,
            ]
        );
    }

    public function getDataLingkungan($type, $tahun, $lokasi, $sungai){
        $filtahun = DB::table('pengawasan_izin')->distinct()->select(DB::raw("YEAR(tgl_pengawasan) AS value"), DB::raw("YEAR(tgl_pengawasan) AS label"))->get();
        $last = PengawasanIzin::orderby('pengawasan_izin_id', 'DESC')->first();

        $db = PengawasanIzin::orderby('pengawasan_izin_id', 'DESC');

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahun;

        if($tahun != '-'){ $db->where(DB::raw("YEAR(tgl_pengawasan)"), $sel_tahun); }

        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
            ],
            'selected' => [
                'tahun' => $sel_tahun,
            ]
        );
    }

    public function getDataPengaduan($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('pengaduan')->distinct()->select('tahun AS value', 'tahun AS label')->orderby('tahun', 'DESC')->get();
        $last = Pengaduan::orderby('pengaduan_id', 'DESC')->first();

        $db = Pengaduan::orderby('pengaduan_id', 'DESC');

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahun;

        if($tahun != '-'){ $db->where('tahun', $sel_tahun); }

        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
            ],
            'selected' => [
                'tahun' => $sel_tahun,
            ]
        );
    }

    public function getDataPembangunanBiogas($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('biogas')->distinct()->select('tahun_pembuatan AS value', 'tahun_pembuatan AS label')->orderby('tahun_pembuatan', 'DESC')->get();
        $last = Biogas::orderby('biogas_id', 'DESC')->first();

        $db = Biogas::orderby('biogas_id', 'DESC');

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahun_pembuatan;

        if($tahun != '-'){ $db->where('tahun_pembuatan', $sel_tahun); }

        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
            ],
            'selected' => [
                'tahun' => $sel_tahun,
            ]
        );
    }

    public function getDataPengawasanB3($type, $tahun, $lokasi, $sungai)
    {
        $fillokasi = DB::table('izin_btiga')->distinct()->select('jenis_kegiatan AS value', 'jenis_kegiatan AS label')->orderby('jenis_kegiatan', 'ASC')->get();
        $last = IzinBtiga::orderby('izin_btiga_id', 'DESC')->first();

        $db = IzinBtiga::orderby('izin_btiga_id', 'DESC');

        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->jenis_kegiatan;

        if($lokasi != '-'){ $db->where('jenis_kegiatan', $sel_lokasi); }

        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'lokasi' => $fillokasi,
            ],
            'selected' => [
                'lokasi' => $sel_lokasi,
            ]
        );
    }

    public function getDataKendaraan($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('emisi_kdr')->distinct()->select('tahunuji AS value', 'tahunuji AS label')->orderby('tahunuji', 'ASC')->get();
        $last = EmisiKdr::orderby('emisi_kdr_id', 'DESC')->first();

        $db = EmisiKdr::orderby('emisi_kdr_id', 'DESC');

        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->tahunuji;

        if($lokasi != '-'){ $db->where('tahunuji', $sel_lokasi); }

        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'lokasi' => $filtahun,
            ],
            'selected' => [
                'lokasi' => $sel_lokasi,
            ]
        );
    }

    public function getDataPembangunLimbahCair($type, $tahun, $lokasi, $sungai)
    {
        $filtahun = DB::table('hasiluji_limbah_cair')->distinct()->select('tahunuji_limbah_cair AS value', 'tahunuji_limbah_cair AS label')->orderby('tahunuji_limbah_cair', 'DESC')->get();
        $fillokasi = DB::table('hasiluji_limbah_cair AS hlc')
            ->distinct()
            ->select(
                'ls.lokasiuji_limbah_cair_id AS value',
                'ls.nama_lokasi AS label',
                'hlc.tahunuji_limbah_cair AS tahun'
            )
            ->leftJoin('lokasiuji_limbah_cair AS ls', 'hlc.lokasiuji_limbah_cair_id', '=', 'ls.lokasiuji_limbah_cair_id')
            ->orderby('ls.nama_lokasi', 'ASC')
            ->get();

        $last = HasilujiLimbahCair::orderby('id', 'DESC')->first();

        $db = HasilujiLimbahCair::orderby('id', 'DESC');

        $sel_tahun = $tahun != "na" ? $tahun : $last->tahunuji_limbah_cair;
        $sel_lokasi = $lokasi != "na" ? $lokasi : $last->lokasiuji_limbah_cair_id;

        if($tahun != '-'){ $db->where('tahunuji_limbah_cair', $sel_tahun); }
        if($lokasi != '-'){ $db->where('lokasiuji_limbah_cair_id', $sel_lokasi); }

        $rows = $db->get();

        return array(
            'data' => $rows,
            'select' => [
                'tahun' => $filtahun,
                'lokasi' => $fillokasi,
            ],
            'selected' => [
                'lokasi' => $sel_lokasi,
                'tahun' => $sel_tahun
            ]
        );
    }

    public function widgets($module='')
    {
        // return [
        //     'hasil_pengujian' => $this->widgetsHasilPengujian(),
        //     'upaya_pengendalian' => $this->widgetsUpayapengendalian(),
        //     'konversi_sumber_daya_alam' => $this->widgetsKonversiSumberdaya(),
        //     'kemitraan_lingkungan_hidup' => $this->widgetsKemitraaanhidup(),
        //     'persampahan' => $this->widgetsPersampahan(),
        //     'trend_pengujian' => $this->widgetsTredPengujuan(),
        //     'data_dampak_lingkungan' => $this->widgetsDampakLingkungan(),
        //     'data_penerimaan_penghargaan' => $this->widgetsDataPenerimaPenghargaan()
        // ];

        switch ($module) {
            case 'hasil_pengujian': return $this->widgetsHasilPengujian(); break;
            case 'upaya_pengendalian': return $this->widgetsUpayapengendalian(); break;
            case 'konversi_sumber_daya_alam': return $this->widgetsKonversiSumberdaya(); break;
            case 'kemitraan_lingkungan_hidup': return $this->widgetsKemitraaanhidup(); break;
            case 'persampahan': return $this->widgetsPersampahan(); break;
            case 'trend_pengujian': return $this->widgetsTredPengujuan(); break;
            case 'data_dampak_lingkungan': return $this->widgetsDampakLingkungan(); break;
            case 'data_penerimaan_penghargaan': return $this->widgetsDataPenerimaPenghargaan(); break;

            default: return []; break;
        }
    }

    public function widgetsHasilPengujian()
    {
        $labels = [["Air","Sungai"], ["Air","Sumur"], ["Air","Situ"], "Ambien", ["Emisi","Cerobong"], ["Emisi","Kendaraan"], "Tanah"];
        $tables = ['hasiluji_sungai','hasiluji_sumur','hasiluji_situ','hasiluji_ambien','hasiluji_cerobong','emisi_kdr','hasiluji_tanah'];

        $datas = [];
        for ($i=0; $i < count($tables) ; $i++) { $datas[$i] = DB::table($tables[$i])->count(); }

        return [
            'labels' => $labels,
            'datas' => $datas
        ];
    }

    public function widgetsUpayapengendalian()
    {
        $labels = [ ["Pengawasan","B3"], ["Pengawasan","Limbah","Cair"], ["Pembangunan","Biogas"], ["Data","Pengaduan"], ["Data","Pengawasan","Izin","Lingkungan"] ];
        $tables = ["izin_btiga", "hasiluji_limbah_cair", "biogas", "pengaduan", "pengawasan_izin"];

        $datas = [];
        for ($i=0; $i < count($tables) ; $i++) { $datas[$i] = DB::table($tables[$i])->count(); }

        return [
            'labels' => $labels,
            'datas' => $datas
        ];
    }

    public function widgetsKonversiSumberdaya()
    {
        $labels = [ ["Mata","Air"], ["Sumur","Pantau"], ["Perusahaan","Penggunaan","Air","Tanah"], ["Sumur","Imbuhan"], ["Hutan","Kota","(Arboretrum)"], "SEHATI", "KEHATI", "PEMERHATI", ["Sumur","Resapan"], ["Data","Realisasi","Penanaman"]];
        $tables = ['mata_air', 'sumur_pantau', 'pemanfaatan_air', 'sumur_imbuhan', 'hutan_kota', 'sehati', 'kehati', 'pemerhati', 'sumur_resapan', 'realisasi_kegiatan'];

        $datas = [];
        for ($i=0; $i < count($tables) ; $i++) {
            if(in_array($tables[$i], ['sehati', 'kehati', 'pemerhati'])){
                $col = ucwords($tables[$i]);
                $datas[$i] = DB::table('skp')->where('kategori', $col)->count();
            }else{
                $datas[$i] = DB::table($tables[$i])->count();
            }
        }

        return [
            'labels' => $labels,
            'datas' => $datas
        ];
    }

    public function widgetsKemitraaanhidup()
    {
        $fill = ['adipura', 'sekolah_adiwiyata'];
        $link = ['/ragam-data_/adipura', '/ragam-data_/sekolah_adiwiyata'];

        $datas = [];
        foreach ($fill as $key) {
            if($key === 'sekolah_adiwiyata'){
                $dd = DB::select(DB::raw("SELECT count(1) AS jml FROM sekolah sk, kelurahan k, kecamatan c WHERE sk.kelurahan_id = k.kelurahan_id  AND k.kecamatan_id = c.kecamatan_id"));
                $datas[$key] = ['name' => 'Sekolah', 'count' => $dd[0]->jml, 'link' => $link[1] ];
            }else{
                $datas[$key] = ['name' => 'Adipura', 'count' => DB::table('adipura')->count(), 'link' => $link[0] ];
            }
        }

        return $datas;
    }

    public function widgetsPersampahan()
    {
        $labels = [ ["Data","TPS3R"], ["Laporan","TPS3R"], ["Data","Sampah","Terangkut"], ["Data","Wajib","Retribusi"], ["Bank","Sampah"] ];
        $tables = ["profil", "lap_tps3r", "terangkut", "retribusi", "basiba"];

        $datas = [];
        for ($i=0; $i < count($tables) ; $i++) {
            if($tables[$i] == 'lap_tps3r'){
                $datas[$i] = DB::select(DB::raw("SELECT COUNT(1) AS jml FROM `profil` INNER JOIN rekap ON profil.profil_id = rekap.profil_id"))[0]->jml;
            }else{
                $datas[$i] = DB::table($tables[$i])->count();
            }
        }

        return [
            'labels' => $labels,
            'datas' => $datas
        ];
    }

    public function widgetsTredPengujuan()
    {
        $labels = ["Sungai", "Situ", "Sumur", ["Emisi","Ambien"], ["Emisi","Cerobong"]];
        $tables = ['sungai', 'situ', 'sumur', 'emisi_ambien', 'emisi_cerobong'];

        $sungai         = DB::select(DB::raw("SELECT 1 FROM hasiluji_sungai GROUP BY lokasiuji_sungai_id, tahunuji_sungai"));
        $Situ           = DB::select(DB::raw("SELECT 1 FROM hasiluji_situ GROUP BY lokasiuji_situ_id, tahunuji_situ"));
        $sumur          = DB::select(DB::raw("SELECT 1 FROM hasiluji_sumur GROUP BY lokasiuji_sumur_id, tahunuji_sumur"));
        $emisi_ambien   = DB::select(DB::raw("SELECT 1 FROM hasiluji_ambien GROUP BY lokasiuji_ambien_id, tahunuji_ambien"));
        $emisi_cerobong = DB::select(DB::raw("SELECT 1 FROM hasiluji_cerobong GROUP BY lokasiuji_cerobong_id, tahunuji_cerobong"));

        $datas[0] = count($sungai);
        $datas[1] = count($Situ);
        $datas[2] = count($sumur);
        $datas[3] = count($emisi_ambien);
        $datas[4] = count($emisi_cerobong);

        return [
            'labels' => $labels,
            'datas' => $datas
        ];
    }

    public function widgetsDampakLingkungan()
    {
        $fill = ["sppl", "ukl_upl", "amdal", "izin"];
        $table = ['sppl_lampau', 'ukl_upl_lampau', 'amdal_lampau', 'izin_lingkungan'];
        $link = ['/ragam-data_/sppl', '/ragam-data_/ukl_upl', '/ragam-data_/amdal', '/ragam-data_/izin'];

        $datas = [];
        $i=0;
        foreach ($fill as $key) {
            $tbl = $table[$i];
            $datas[$key] = ['name' => $tbl, 'count' => DB::table($tbl)->count(), 'link' => $link[$i] ];
            $i++;
        }

        return $datas;
    }

    public function widgetsDataPenerimaPenghargaan()
    {
        $fill = [
            [ 'table' => 'penerimaan_penghargaan', 'filter' => 'tahun' ]
        ];

        $rows = [];
        $i = 0;
        foreach ($fill as $key) {
            $tabel = $fill[$i]['table'];
            $filter = $fill[$i]['filter'];
            $filter_db = DB::table($tabel)->select($filter)->distinct()->orderby($filter, 'DESC')->get();

            $a = 0;
            $filter_arr = [];
            foreach ($filter_db as $key) {
                $filter_arr[$a] = $key->$filter;
                $a++;
            }

            $rows[$i]['rows'] = DB::table($tabel)->get();
            $rows[$i]['filter'] = ['column' => $filter, 'array' => $filter_arr];
            $i++;
        }

        return $rows;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * ref tahun uji.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function refTahunUji($tahun,Request $request)
    {
        //

    }
}
