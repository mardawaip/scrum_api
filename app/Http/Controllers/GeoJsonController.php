<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DB;

class GeoJsonController extends Controller
{
    public function getGeoJsonBasic(Request $request)
    {
        $url = $request->url;

        if(preg_match("/.php/i", $url)){
            $json = $this->getgeoJsonPHP($url,$request);
        }else{
            $path = storage_path().$url;
            $json = json_decode(file_get_contents($path), true);
        }

        return $json;
    }

    public function getgeoJsonPHP($url,$request)
    {
        $path = storage_path().$url;
        $query = $this->get_string_between(file_get_contents($path),'$sql = "','";');
        // return $query;
        // $query = $this->getQuery($url);
        $query = str_replace('$path',$request->getSchemeAndHttpHost().'/storage/ragamdata',$query);
        $db = DB::connection('mysql')->select($query);

        # Build GeoJSON feature collection array
        $geojson = array(
           'type'      => 'FeatureCollection',
           'features'  => array()
        );

        # Loop through rows to build feature arrays
        foreach ($db as $key) {
            $properties = $key;
            # Remove x and y fields from properties (optional)
            unset($properties->lat);
            unset($properties->lng);
            $feature = array(
                'type' => 'Feature',
                'geometry' => array(
                    'type' => 'Point',
                    'coordinates' => array(
                        $key->x,
                        $key->y
                    )
                ),
                'properties' => $properties
            );
            # Add feature arrays to feature collection array
            array_push($geojson['features'], $feature);
        }

        return json_encode($geojson, JSON_UNESCAPED_SLASHES);
    }

    public function getQuery($url='')
    {
        switch ($url) {
            case '/geojson/sumur_geojson.php':
                $sql = "SELECT sm.lokasiuji_sumur_id, sm.alamat, sm.nama_lokasi, sm.kelurahan_id, k.kecamatan_id, c.kecamatan_nama, k.kelurahan_nama, sm.lat, sm.lng, sm.icon_map, sm.`marker-color`, sm.`marker-symbol`, sm.`marker-size`,CONCAT('<b>LOKASI UJI SUMUR</b><br>',sm.nama_lokasi,'<br>', sm.alamat,'<br>',c.kecamatan_nama,'<br>',k.kelurahan_nama) As description, sm.lng AS x, sm.lat AS y FROM lokasiuji_sumur sm, kelurahan k, kecamatan c where sm.kelurahan_id = k.kelurahan_id AND k.kecamatan_id = c.kecamatan_id";
                break;
            case '/geojson/sumur_bosel_geojson.php':
                $sql = "SELECT sm.lokasiuji_sumur_id, sm.alamat, sm.nama_lokasi, sm.kelurahan_id, k.kecamatan_id, c.kecamatan_nama, k.kelurahan_nama, sm.lat, sm.lng, sm.icon_map, sm.`marker-color`, sm.`marker-symbol`, sm.`marker-size`,CONCAT('<b>LOKASI UJI SUMUR</b><br>',sm.nama_lokasi,'<br>', sm.alamat,'<br>',c.kecamatan_nama,'<br>',k.kelurahan_nama) As description, sm.lng AS x, sm.lat AS y  FROM lokasiuji_sumur sm, kelurahan k, kecamatan c where sm.kelurahan_id = k.kelurahan_id AND k.kecamatan_id = c.kecamatan_id AND c.kecamatan_id = 1";
            
            default:
                // code...
                break;
        }

        return $sql;
    }

    function get_string_between($string, $start, $end){
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) return '';
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
