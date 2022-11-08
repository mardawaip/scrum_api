<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use DB;

class MgnMenuController extends Controller
{
    public function GET_menu(Request $request)
    {
        $query = DB::table('menu')
        ->select(
            "menu.kode AS id",
            "menu.*"
        )                
        ->where('menu.type_menu', $request->type_menu)
        ->orderBy("menu.nomor_urut", "ASC");

        if($request->peran_id){
            $query
            ->leftJoin('role_menu AS menu_peran', 'menu.menu_id', '=', 'menu_peran.menu_id')
            ->where('menu_peran.role_id', $request->peran_id)
            ->where('menu_peran.soft_delete', 0);
        }

        $menuAll = $query->get();
        $menu = $this->getArrayFiltered('tingkat_menu', '1', $menuAll);

        $i = 0;
        $rows = [];
        foreach ($menu as $key) {
            $rows[$i] = $key;

            if($key->type == "collapse"){
                $submenu = $this->getArrayFiltered_2(['tingkat_menu' => '2', 'induk_menu_id' => $key->menu_id], $menuAll);

                foreach ($submenu as $submenukey) {
                    $i++;
                    $rows[$i] = $submenukey;

                    if($submenukey->type == "collapse"){
                        $subsubmenu = $this->getArrayFiltered_2(['tingkat_menu' => '3', 'induk_menu_id' => $submenukey->menu_id], $menuAll);

                        foreach ($subsubmenu as $subsubmenukey) {
                            $i++;
                            $rows[$i] = $subsubmenukey;

                            if($subsubmenukey->type == "collapse"){
                                $subsubsubmenu = $this->getArrayFiltered_2(['tingkat_menu' => '4', 'induk_menu_id' => $subsubmenukey->menu_id], $menuAll);

                                foreach ($subsubsubmenu as $subsubsubmenukey) {
                                    $i++;
                                    $rows[$i] = $subsubsubmenukey;
                                }
                            }
                        }
                    }
                }
            }
            $i++;
        }

        $sql = "SELECT
            `category`.`category_id` AS `menu_id`,
            concat( 'kode-', `category`.`url` ) AS `kode`,
            `category`.`category_name` AS `title`,
            1 AS `type`,
            1 AS `icon`,
            1 AS `auth`,
            concat( 'portal/', `category`.`url` ) AS `url`,
            `category`.`parent` AS `induk_menu_id`,
            `category`.`sort` AS `nomor_urut`,
            CASE WHEN `category`.`parent` = 0 THEN 1 ELSE 2 END AS `tingkat_menu`, 0 AS `soft_delete`, 1 AS `dashboard`,
            `category`.`color` AS `dashboard_icon`,
            `category`.`color` AS `dashboard_icon_hover`,
            'portal' AS `type_menu`,
            '-' AS `status`,
            curdate() AS `created_at`,
            curdate() AS `updated_at` 
        FROM
            `category`";

        $portal = DB::select(DB::raw($sql));

        $ragam_data = DB::table('menu')
            ->where('type_menu', 'ragam_data')
            ->orderBy("tingkat_menu", "ASC")
            ->orderBy("nomor_urut", "ASC")
            ->get();

        $return = [
            'filter'        => $request->peran_id,
            'rows'          => $rows,
            'portal'        => $portal,
            'ragam_data'    => $ragam_data,
            'count_all'     => count($menu)
        ];

        return $return;
    }

    public function getSelectMenu(Request $request)
    {
        $peran = $this->GET_peran();
        $menuHeader = $this->GET_menu_header();
        $jenisMenu = ['rows' => ['manajemen']]; //$this->GET_jenis_menu();

        return [
            'peran' => $peran['rows'],
            'menu_header' => $menuHeader['rows'],
            'jenis_menu' => $jenisMenu['rows']
        ];
    }

    public function GET_peran()
    {
        $peran = DB::table('roles')->get();

        return [
            'count' => count($peran),
            'rows' => $peran
        ];
    }

    public function GET_menu_header()
    {
        $peran = DB::table('menu')
            ->select('menu.*', 'head.title AS title_head')
            ->leftJoin('menu AS head', 'menu.induk_menu_id', '=', 'head.menu_id')
            ->where('menu.type', 'collapse')
            ->orderby('menu.induk_menu_id', 'ASC')
            ->orderby('menu.title', 'ASC')
            ->where('menu.soft_delete', 0)
            ->get();

        return [
            'count' => count($peran),
            'rows' => $peran
        ];
    }

    public function GET_jenis_menu()
    {
        $jenis_menu = DB::table('ref.jenis_menu')->where('soft_delete', 0)->get();

        return [
            'count' => count($jenis_menu),
            'rows' => $jenis_menu
        ];
    }

    public function GET_menu_peran(Request $request)
    {
        $menu_peran = DB::table('role_menu')
            ->where('menu_id', $request->menu_id)
            ->get();

        return [
            'count' => count($menu_peran),
            'rows' => $menu_peran
        ];
    }

    public function Simpan_menu_peran(Request $request)
    {
        $menu_id = $request->menu_id;
        $selected = $request->selected;

        $menu_peran = DB::table('role_menu');
        $delete = $menu_peran->where('menu_id', $menu_id)->delete();

        foreach ($selected as $key) {
            $role_id = $key;
            $data[] = [
                'menu_id' => $menu_id,
                'role_id' => $role_id
            ];
        }

        if(count($data) != 0){
            $insert = $menu_peran->insert($data);
        }
    }

    public function GET_permenu(Request $request)
    {
        $menu_id = $request->menu_id;

        $menu = DB::table('menu AS menu')
            ->where('menu_id', $request->menu_id)
            ->get();

        return [
            'count' => count($menu),
            'rows' => count($menu) == 1 ? $menu[0] : []
        ];
    }

    public function get_ref_IconColor(Request $request)
    {
        $color = DB::connection("sqlsrv_2")
            ->table("auth.menu")
            ->distinct()
            ->where("soft_delete", 0)
            ->select(DB::raw("dashboard_icon_hover AS color"))
            ->whereNotNull("dashboard_icon_hover")
            ->get();

        $icon = DB::connection("sqlsrv_2")
            ->table("auth.menu")
            ->distinct()
            ->where("soft_delete", 0)
            ->select(DB::raw("dashboard_icon AS icon"))
            ->whereNotNull("dashboard_icon")
            ->get();

        $menu_induk = DB::connection("sqlsrv_2")
            ->table("auth.menu")
            ->distinct()
            ->where("soft_delete", 0)
            ->select("menu_id", "title")
            ->where("type", "collapse")
            ->get();

        return [
            "color" => $color,
            "icon" => $icon,
            "menu_induk" => $menu_induk
        ];
    }

    public function simpan_permenu(Request $request)
    {
        $cek = DB::table('menu')->select('menu_id')->limit(1)->orderBy('menu_id', 'DESC')->get(); //Str::uuid();
        $no = count($cek) >= 1 ? ($cek[0]->menu_id + 1) : 1;

        $data = [
            'menu_id'               => $no,
            'kode'                  => str_replace(' ', '-', strtolower($request->title)),
            'title'                 => $request->title,
            'type'                  => $request->type,
            'icon'                  => $request->icon,
            'auth'                  => null,
            'url'                   => $request->url,
            'induk_menu_id'         => $request->induk_menu_id,
            'nomor_urut'            => $request->nomor_urut,
            'dashboard'             => $request->dashboard,
            'dashboard_icon'        => $request->dashboard_icon,
            'dashboard_icon_hover'  => $request->dashboard_icon_hover,
            'type_menu'             => $request->type_menu,
            'tingkat_menu'          => $request->tingkat_menu,
            'status'                => $request->status,
            'soft_delete'           => 0
        ];

        $menu = DB::table('menu')->insert($data);
        $msg = $menu ? 'success' : 'error';

        return ['msg' => $msg, 'id' => $no];
    }

    public function update_permenu(Request $request)
    {
        $menu_id = $request->menu_id;

        $data = [
            'menu_id'               => $request->menu_id,
            'kode'                  => str_replace(' ', '-', strtolower($request->title)),
            'title'                 => $request->title,
            'type'                  => $request->type,
            'icon'                  => $request->icon,
            'url'                   => $request->url,
            'induk_menu_id'         => $request->induk_menu_id,
            'nomor_urut'            => $request->nomor_urut,
            'dashboard'             => $request->dashboard,
            'dashboard_icon'        => $request->dashboard_icon,
            'dashboard_icon_hover'  => $request->dashboard_icon_hover,
            'type_menu'             => $request->type_menu,
            'tingkat_menu'          => $request->tingkat_menu,
            'status'                => $request->status,
            'soft_delete'           => 0
        ];
        
        $menu = DB::table('menu')
            ->where('menu_id', $menu_id)
            ->update($data);
        $msg = $menu ? 'success' : 'error';
        return ['msg' => $msg, 'id' => $menu_id];
    }

    public function delete_permenu(Request $request)
    {
        $menu_id = $request->menu_id;

        $menu_peran = DB::table('auth.menu_peran')
            ->where('menu_id', $menu_id)
            ->delete();

        $menu = DB::table('auth.menu')
            ->where('menu_id', $menu_id)
            ->delete();
        return $menu ? 'success' : 'error';
    }

    public function nonaktif_permenu(Request $request)
    {
        $menu = DB::table('auth.menu')
            ->where('menu_id', $request->menu_id)
            ->update([
                'soft_delete' => ($request->soft_delete == 0 ? 1 : 0)
            ]);
        return $menu ? 'success' : 'error';
    }

    public function getIcon(Request $request)
    {
        $icon = DB::table('ref.icon')->select('icon AS name')->orderby('icon', 'ASC')->get();

        return $icon;
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

    public function getArrayFiltered_2($arrayFilter, $array) {
        $filtered_array = array();
        foreach ($array as $value) {
            if (($value->tingkat_menu == $arrayFilter['tingkat_menu']) && ($value->induk_menu_id == $arrayFilter['induk_menu_id'])) {
                $filtered_array[] = $value;
            }
        }

        return $filtered_array;
    }
}
