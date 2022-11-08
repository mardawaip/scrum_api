<?php

namespace App\Http\Controllers;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use DB;

class MenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Menu::all();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode' => '',
            'title' => '',
            'type' => '',
            'icon' => '',
            'auth' => '',
            'url' => '',
            'induk_menu_id' => '',
            'nomor_urut' => '',
            'tingkat_menu' => '',
            'soft_delete' => '',
            'dashboard' => '',
            'dashboard-icon' => '',
            'dashboard-icon-hover' => '',
            'type-menu' => '',
            'status' => ''
        ]);

        return Menu::create($request->all());
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // return "aris";
        return Menu::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
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
        $menu = Menu::find($id);
        $menu->update($request->all());
        return $menu;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        return Menu::destroy($id);
    }

    public function getMenu($parent)
    {
        $parent_ = implode("','", $parent);
        $parent__ = "'".$parent_."'";

        $sql = "SELECT
            concat( 'kode-', `category`.`url` ) AS `id`,
            `category`.`category_id` AS `menu_id`,
            concat( 'kode-', `category`.`url` ) AS `kode`,
            `category`.`category_name` AS `title`,
            concat( 'portal/', `category`.`parent`, '/', `category`.`category_id` ) AS `url`,
            `category`.`parent` AS `induk_menu_id`,
            (SELECT count(1) FROM category AS ct WHERE ct.parent = menu_id) AS count,
            (CASE WHEN (SELECT count(1) FROM category AS ct WHERE ct.parent = menu_id) = 0 THEN 'item' ELSE 'collapse' END) AS `type`,
            `category`.`sort` AS `nomor_urut`,
            CASE WHEN `category`.`parent` = 0 THEN 1 ELSE 2 END AS `tingkat_menu`,
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

    public function portal()
    {
        // $sql = "SELECT
        //     concat( 'kode-', `category`.`url` ) AS `id`,
        //     `category`.`category_id` AS `menu_id`,
        //     concat( 'kode-', `category`.`url` ) AS `kode`,
        //     `category`.`category_name` AS `title`,
        //     (CASE WHEN (SELECT count(1) FROM `category` WHERE `category`.`parent` = `menu_id`) > 1 THEN 'group' ELSE 'item' END) AS `type`,
        //     concat( 'portal/', `category`.`parent`, '/', `category`.`category_id` ) AS `url`,
        //     `category`.`parent` AS `induk_menu_id`,
        //     `category`.`sort` AS `nomor_urut`,
        //     CASE WHEN `category`.`parent` = 0 THEN 1 ELSE 2 END AS `tingkat_menu`,
        //     0 AS `soft_delete`,
        //     1 AS `dashboard`,
        //     `category`.`color` AS `dashboard_icon`,
        //     `category`.`color` AS `dashboard_icon_hover`,
        //     'portal' AS `type_menu`,
        //     '-' AS `status`,
        //     curdate() AS `created_at`,
        //     curdate() AS `updated_at` 
        // FROM
        //     `category`
        // WHERE
        //     `category`.`category_id` NOT IN (8, 65)
        // ORDER BY category.sort ASC";

        // $portal = DB::select(DB::raw($sql));

        $portal = $this->getMenu([0]);

        $beranda = [
            "id"                    => 'beranda',
            "menu_id"               => 0,
            "kode"                  => "beranda",
            "title"                 => "Beranda",
            "type"                  => "item",
            "url"                   => "/beranda",
            "induk_menu_id"         => 0,
            "nomor_urut"            => 0,
            "tingkat_menu"          => 1,
        ];

        // $layanan = [
        //     "id"                    => 'layanan',
        //     "menu_id"               => 'layanan',
        //     "kode"                  => "layanan",
        //     "title"                 => "Layanan",
        //     "type"                  => "collapse",            
        //     "induk_menu_id"         => 0,
        //     "nomor_urut"            => 0,
        //     "tingkat_menu"          => 1,
        // ];

        // $layanan_informasi = [
        //     "id"                    => 'informasi-dan-pengaduan',
        //     "menu_id"               => 'layanan',
        //     "kode"                  => "informasi-dan-pengaduan",
        //     "title"                 => "Informasi dan Pengaduan",
        //     "type"                  => "item",
        //     "url"                   => "/layanan/informasi-dan-pengaduan",
        //     "induk_menu_id"         => 'layanan',
        //     "nomor_urut"            => 0,
        //     "tingkat_menu"          => 1,
        // ];

        // $layanan_pengaduan = [
        //     "id"                    => 'data-pengaduan',
        //     "menu_id"               => 9992,
        //     "kode"                  => "data-pengaduan",
        //     "title"                 => "Data Pengaduan",
        //     "type"                  => "item",
        //     "url"                   => "/layanan/data-pengaduan",
        //     "induk_menu_id"         => 'layanan',
        //     "nomor_urut"            => 0,
        //     "tingkat_menu"          => 1,
        // ];


        $ragam_data = [
            "id"                    => 'ragam-data',
            "menu_id"               => 'ragam-data',
            "kode"                  => "ragam-data",
            "title"                 => "Ragam Data",
            "type"                  => "link",
            "url"                   => "https://dlh-cms.mardawa.id/",
            'target'                => '_blank',
            "induk_menu_id"         => 0,
            "nomor_urut"            => 0,
            "tingkat_menu"          => 1,
        ];

        $galeri = [
            "id"                    => 'galeri',
            "menu_id"               => 'galeri',
            "kode"                  => "galeri",
            "title"                 => "Galeri",
            "type"                  => "item",
            "url"                   => "/galeri",
            "induk_menu_id"         => 0,
            "nomor_urut"            => 0,
            "tingkat_menu"          => 1,
        ];

        $web_gis = [
            "id"                    => 'web_gis',
            "menu_id"               => 'web_gis',
            "kode"                  => "web_gis",
            "title"                 => "Web GIS",
            "type"                  => "link",
            "url"                   => "https://dlh-gis.mardawa.id/",
            'target'                => '_blank',
            "induk_menu_id"         => 0,
            "nomor_urut"            => 0,
            "tingkat_menu"          => 1,
        ];        

        $menu_ = [];
        $induk_menu = [];
        $menu_[0] = (object)$beranda;
        $o = 1;
        foreach ($portal as $value) {            
            $menu_[$o] = $value;
            $induk_menu[] = $value->menu_id;

            $o++;
        }

        $last = count($menu_);

        // $menu_[$last+1] = (object)$layanan;
        $menu_[$last+1] = (object)$galeri;
        $menu_[$last+2] = (object)$ragam_data;
        $menu_[$last+3] = (object)$web_gis;

        $menu = $menu_;

        $menu2 = $this->getMenu($induk_menu);

        $induk_menu2 = [];
        foreach ($menu2 as $key2) {
            $induk_menu2[] = $key2->menu_id;
        }

        // $last2 = count($menu2);

        // $menu2[$last2+1] = (object)$layanan_informasi;
        // $menu2[$last2+2] = (object)$layanan_pengaduan;

        $menu3 = $this->getMenu($induk_menu2);

        $i = 0;
        $rows = [];
        foreach ($menu as $key) {
            $rows[$i] = $key;

            if($key->type == "collapse"){
                unset($rows[$i]->url);
                $submenu = $this->getArrayFiltered_2(['induk_menu_id' => $key->menu_id], $menu2);

                $a = 0;
                foreach ($submenu as $submenukey) {
                    $submenu[$a] = $submenukey;

                    if($submenukey->type == "collapse"){
                        $subsubmenu = $this->getArrayFiltered_2(['induk_menu_id' => $submenukey->menu_id], $menu3);

                        $submenu[$a]->children = $subsubmenu;
                    }

                    $a++;
                }

                $rows[$i]->children = $submenu;
            }
            $i++;
        }

        return $rows;
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
}
