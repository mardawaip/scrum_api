<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scrum;
use App\Models\Aplikasi;
use App\Models\ScrumSettings;
use Illuminate\Support\Str;

class ScrumController extends Controller
{
    public function index(Request $request)
    {
        // code...
    }

    public function getScrum(Request $request)
    {
        return Scrum::orderBy('created_at', 'DESC')->get();
    }

    public function addScrum($value='')
    {
        $uuid = Str::uuid();

        $dataAplikasi = [
            'aplikasi_id'   => $uuid,
            'nama'          => 'Untitled Board',
        ];

        $dataSetting = [
            'setting_id'    => $uuid,
        ];

        $dataScrum = [
            'scrum_id'      => $uuid,
            'aplikasi_id'   => $uuid,
            'setting_id'    => $uuid
        ];
        
        Aplikasi::create($dataAplikasi);
        ScrumSettings::create($dataSetting);
        return Scrum::create($dataScrum);
    }

    public function updateScrum(Request $request, $id)
    {
        $title = $request->title;

        $aplikasi = Aplikasi::find($id);
        $aplikasi->nama = $title;
        $aplikasi->save();

        return Scrum::find($id);
    }

    public function getScrumDetail(Request $request, $id)
    {
        return Scrum::find($id);
    }

    public function getAplikasiDetail(Request $request, $id)
    {
        $Scrum = Scrum::find($id);

        $aplikasi_id = $Scrum->aplikasi_id;

        $aplikasi = Aplikasi::find($aplikasi_id);

        return [
            'aplikasi' => $aplikasi
        ];
    }
}
