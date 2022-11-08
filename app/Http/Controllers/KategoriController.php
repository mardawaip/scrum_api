<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class KategoriController extends Controller
{
    public function getMenu($parent, $int)
    {
        $parent_ = implode("','", $parent);
        $parent__ = "'".$parent_."'";

        $sql = "SELECT
            concat( 'kode-', `category`.`category_id` ) AS `id`,
            `category`.`category_id` AS `menu_id`,
            concat( 'kode-', `category`.`category_id` ) AS `kode`,
            `category`.`category_name` AS `title`,
            concat( 'portal/', `category`.`parent`, '/', `category`.`category_id` ) AS `url`,
            `category`.`parent` AS `induk_menu_id`,
            (SELECT count(1) FROM category AS ct WHERE ct.parent = menu_id) AS count,
            (CASE WHEN (SELECT count(1) FROM category AS ct WHERE ct.parent = menu_id) = 0 THEN 'item' ELSE 'collapse' END) AS `type`,
            `category`.`sort` AS `nomor_urut`,
            $int AS `tingkat_menu`,
            0 AS `soft_delete`,
            1 AS `dashboard`,
            `category`.`color` AS `dashboard_icon`,
            `category`.`color` AS `dashboard_icon_hover`,
            'portal' AS `type_menu`,
            '-' AS `status`,
            curdate() AS `created_at`,
            curdate() AS `updated_at` 
        FROM
            `category`
        WHERE
            `category`.`parent` IN ($parent__) AND
            `category`.`category_id` NOT IN (8, 65)
        ORDER BY
            category.sort ASC";

        return $portal = DB::select(DB::raw($sql));
    }

    public function get(Request $request)
    {
        $menu1 = $this->getMenu([0], 1);

        $induk_menu = [];
        foreach ($menu1 as $value) { $induk_menu[] = $value->menu_id; }

        $menu2 = $this->getMenu($induk_menu, 2);

        $induk_menu2 = [];
        foreach ($menu2 as $key2) { $induk_menu2[] = $key2->menu_id; }

        $menu3 = $this->getMenu($induk_menu2, 3);

        $i = 0;
        $rows = [];
        foreach ($menu1 as $key) {
            $rows[$i] = $key;

            if($key->type == "collapse"){
                $submenu = $this->getArrayFiltered_2(['induk_menu_id' => $key->menu_id], $menu2);

                foreach ($submenu as $submenukey) {
                    $i++;
                    $rows[$i] = $submenukey;

                    if($submenukey->type == "collapse"){
                        $subsubmenu = $this->getArrayFiltered_2(['induk_menu_id' => $submenukey->menu_id], $menu3);

                        foreach ($subsubmenu as $subsubmenukey) {
                            $i++;
                            $rows[$i] = $subsubmenukey;
                        }
                    }
                }
            }
            $i++;
        }

        return [
            'rows' => $rows,
            'count' => count($rows),
            'count_all' => count($rows)
        ];
    }

    public function getSelectKategori(Request $request)
    {
        $menu_header = DB::table('category')
            ->select(
                "category.category_id AS value",
                "category.category_name AS label",
                "h.category_name AS head"
            )
            ->leftJoin('category AS h', 'h.category_id', '=', 'category.parent')
            ->orderby('category.parent', 'ASC')
            ->orderby('category.sort', 'ASC')
            ->get();

        return [
            'menu_header' => $menu_header
        ];
    }

    public function getperkategori(Request $request)
    {
        $kategoti = DB::table('category')->where('category_id', $request->category_id)->first();
        return ['rows' => $kategoti ];
    }

    public function getArrayFiltered_2($arrayFilter, $array) {
        $filtered_array = array();
        foreach ($array as $value) {
            if ($value->induk_menu_id == $arrayFilter['induk_menu_id']){
                $filtered_array[] = $value;
            }
        }

        return $filtered_array;
    }

    public function getArrayFiltered($aFilterKey, $aFilterValue, $array) {
        $filtered_array = array();
        foreach ($array as $value) {
            if (isset($value[$aFilterKey])) {
                if ($value[$aFilterKey] == $aFilterValue) {
                    $filtered_array[] = $value;
                }
            }
        }

        return $filtered_array;
    }

    public function add(Request $request)
    {
        $category_id_new = DB::table("category")->orderby("category_id", "DESC")->first()->category_id + 1;
        $data = [
            'category_id' => $category_id_new,
            'category_name' => $request->category_name,
            'parent' => $request->parent,
            'sort' => $request->sort,
            'type' => $request->type,
            'color' => $request->color,
        ];

        $insert = DB::table('category')->insert($data);

        return [
            'id' => $category_id_new,
            'msg' => ($insert ? 'success' : 'error')
        ];
    }

    public function update(Request $request)
    {
        $data = [
            'category_name' => $request->category_name,
            'parent' => $request->parent,
            'sort' => $request->sort,
            'type' => $request->type,
            'color' => $request->color,
        ];

        $update = DB::table('category')->where('category_id', $request->category_id)->update($data);

        return [
            'id' => $request->category_id,
            'msg' => ($update ? 'success' : 'error')
        ];
    }

    public function delete(Request $request)
    {
        $cek = DB::table('post')->where('category_id', $request->category_id)->count();

        if($cek >= 1){
            $msg = 'not_delete';
        }else{
            $delete = DB::table('category')->where('category_id', $request->category_id)->delete();
            $msg = $delete ? 'success' : 'error';
        }

        return [
            'id' => $request->category_id,
            'msg' => $msg
        ];
    }
}
