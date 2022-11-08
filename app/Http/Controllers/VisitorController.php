<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\User;
use DB;

class VisitorController extends Controller
{
    public function setvisitor(Request $request)
    {
        $post = $request->all();
        $user = User::find(2);
        $log = $request->visitor()->setVisitor($user)->visit();

        $log = json_decode($log);
        $data = [
            'method' => $log->method,
            'request' => json_encode($log->request),
            'url' => $log->url,
            'referer' => $log->referer,
            'languages' => json_encode($log->languages),
            'useragent' => json_encode($log->useragent),
            // 'headers' => json_encode($log->headers),
            'device' => json_encode($log->device),
            'platform' => json_encode($log->platform),
            'browser' => json_encode($log->browser),
            'ip' => $log->ip,
        ];        
        
        return Visitor::create($data);
    }

    public function getVisitor()
    {
        $pengunjung = DB::select(DB::raw("SELECT count(distinct `visitor`.`ip`) AS `uniq_visitor` from `visitor` WHERE NOT ip = '127.0.0.1' "))[0]->uniq_visitor;
        $dikunjungi = Visitor::whereNot('ip', '127.0.0.1')->count();

        return [
            'pengunjung' => $pengunjung,
            'dikunjungi' => $dikunjungi,
        ];
    }

    public function getWidgets(Request $request)
    {
        $visitor = $this->widgetsVisitor();
        $device = $this->widgetsDevice();
        $table = $this->widgetsTable();

        return [
            'visitors' => $visitor,
            'device' => $device,
            'table' => $table
        ];
    }

    public function widgetsVisitor()
    {
        $ranges__ = DB::table('visitor')->select(DB::raw("DISTINCT YEAR(created_at) AS tahun"))->get();

        $ranges = [];
        $series = [];

        foreach ($ranges__ as $key) {
            $tahun = $key->tahun;

            $data = DB::table('visitor')
                    ->select(
                        DB::raw("DATE( created_at ) AS x"),
                        DB::raw("count( id ) AS y")
                    )
                    ->where(DB::raw("YEAR(visitor.created_at)"), $tahun)
                    ->whereNot('visitor.ip', '127.0.0.1')
                    ->groupBy(DB::raw("DATE( visitor.created_at )"))
                    ->get();

            $ranges[$tahun] = $tahun;
            $series[$tahun] = [
                [
                    'name' => 'Visitors',
                    'data' => $data,
                ]
            ];
        }

        return [
            'ranges' => $ranges,
            'series' => $series
        ];
    }

    public function widgetsDevice()
    {
        $db = DB::table('visitor')
            ->select(
                "platform",
                DB::raw("count(id) AS jml")
            )
            ->whereNot('visitor.ip', '127.0.0.1')
            ->groupBy('platform')
            ->get();

        $countAll = DB::table('visitor')->count();
        $series = [];
        $labels = [];

        $i = 0;
        foreach ($db as $key) {
            $labels[$i] = str_replace('"', '', $key->platform);
            $series[$i] = $key->jml; //number_format(($key->jml / $countAll) * 100);

            $i++;
        }

        return [
            "uniqueVisitors" => $countAll,
            "series" => $series,
            "labels" => $labels
        ];
    }

    public function widgetsTable()
    {
        $db = DB::table('visitor')
            ->select(
                DB::raw("DATE( created_at ) AS tanggal"),
                DB::raw("count( id ) AS pengunjung")
            )
            ->whereNot('visitor.ip', '127.0.0.1')
            ->groupBy(DB::raw("DATE( visitor.created_at )"))
            ->get();

        return $db;
    }
}
