<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TahunujiSungai;
use App\Models\Sppl;
use App\Models\Amdal;
use App\Models\UKLUPL;
use DB;

class MgnRagamDataController extends Controller
{
    public function index(Request $request, $module)
    {
        switch ($module) {
            case 'sungai': return $this->sungai($request); break;
            case 'perizinan_sppl': return $this->get_perizinan_sppl($request); break;
            case 'perizinan_amdal': return $this->get_perizinan_amdal($request); break;
            case 'perizinan_ukl_upl': return $this->get_perizinan_ukl_upl($request); break;
            
            default: return []; break;
        }
    }

    public function getPerdata(Request $request, $module, $id)
    {
        switch ($module) {
            case 'perizinan_sppl': return $this->getper_perizinan_sppl($request, $id); break;
            case 'perizinan_amdal': return $this->getper_perizinan_amdal($request, $id); break;
            case 'perizinan_ukl_upl': return $this->getper_perizinan_ukl_upl($request, $id); break;
            
            default: return []; break;
        }
    }

    public function sungai($request)
    {
        $limit = $request->limit ? $request->limit : 10;
        
        return TahunujiSungai::orderBy('tahunuji_sungai', 'DESC')->paginate($limit);
    }

    public function JoinPersyaratan($data)
    {
        $id = $data->id;
        $jenisizin_id = $data->jenisizin_id;

        $db = DB::table('izin_persyaratan AS ip')
            ->select('persyaratan.persyaratan','us.dokumen_id', 'dm.file AS file', 'dm.file_name AS file_name')
            ->leftJoin('persyaratan', 'ip.persyaratan_id', '=', 'persyaratan.persyaratan_id')
            ->leftJoin('upload_syarat AS us', function($join) use($id) {
                $join->on('persyaratan.persyaratan_id','=','us.persyaratan_id')
                ->where('us.permohonan_id', $id);
            })
            ->leftJoin('direktori_member AS dm', 'us.dokumen_id', '=', 'dm.dokumen_id')
            ->where('ip.jenisizin_id', $jenisizin_id)
            ->get();

        return $db;
    }

    // SPPL
    public function get_perizinan_sppl($request)
    {
        $limit = $request->limit ? $request->limit : 10;

        return Sppl::orderBy('permohonan_id', 'DESC')->whereIn('status_perizinan', [0,1])->paginate($limit);
    }

    public function getper_perizinan_sppl($request, $id)
    {
        $sppl = Sppl::find($id);
        $sppl->join_persyaratan = $this->JoinPersyaratan($sppl);

        return $sppl;
    }
    // END SPPL

    // AMDAL
    public function get_perizinan_amdal($request)
    {
        $limit = $request->limit ? $request->limit : 10;

        return Amdal::orderBy('permohonan_id', 'DESC')->whereIn('status_perizinan', [0,1])->paginate($limit);
    }

    public function getper_perizinan_amdal($request, $id)
    {
        $sppl = Amdal::find($id);
        $sppl->join_persyaratan = $this->JoinPersyaratan($sppl);

        return $sppl;
    }
    // END AMDAL

    // UKL UPL
    public function get_perizinan_ukl_upl($request)
    {
        $limit = $request->limit ? $request->limit : 10;

        return UKLUPL::orderBy('permohonan_id', 'DESC')->whereIn('status_perizinan', [0,1])->paginate($limit);
    }

    public function getper_perizinan_ukl_upl($request, $id)
    {
        $sppl = UKLUPL::find($id);
        $sppl->join_persyaratan = $this->JoinPersyaratan($sppl);

        return $sppl;
    }
    // END UKL UPL
}
