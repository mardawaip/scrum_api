<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class ManajemenKontenController extends Controller
{
    public function get_page(Request $request)
    {
        $link = $request->link;

        $menu = DB::table('auth.menu AS menu')
            ->select(
                'menu.*',
                'mm.table',
                'mm.form_hidden'
            )
            ->leftJoin('manajemen_module AS mm', 'menu.menu_id', '=', 'mm.menu_id')
            ->where('menu.url', $link)
            ->first();

        $setting = (array)$menu;

        // GET STRUKTUR TABLE
        if($menu && $menu->table != ''){
            $table = $menu->table;
            $sql = "
                SELECT
                    COLUMN_NAME AS column_name,
                    DATA_TYPE AS data_type,
                    CHARACTER_MAXIMUM_LENGTH AS data_length
                FROM
                    INFORMATION_SCHEMA.COLUMNS
                WHERE
                    TABLE_SCHEMA = 'dbo'
                    AND TABLE_NAME='$table'
                ORDER BY
                    ORDINAL_POSITION ASC";
            $table_struktur = DB::select(DB::raw($sql));
            $setting['table_struktur'] = $table_struktur;

            if(count($table_struktur) != 0){
                $table_rows = DB::table($table)->where('soft_delete', 0)->get();
                $setting['table_rows'] = $table_rows;
            }
        }

        if($menu && $menu->table != ''){
            $table = $menu->table;
            $manajemen_form = DB::table('manajemen_form')->where('table', $table)->get();

            if(count($manajemen_form) != 0){
                $setting['form'] = $table;
                $setting['form_detail'] = $manajemen_form;
                $setting['form_hidden'] = $menu->form_hidden ? json_decode($menu->form_hidden) : [];
            }
        }

        return $setting;
    }

    public function tableSimpan(Request $request)
    {
        $table = $request->table;
        $field = (array)$request->field;

        $sql = "SELECT
                COLUMN_NAME AS column_name,
                DATA_TYPE AS data_type,
                CHARACTER_MAXIMUM_LENGTH AS data_length
            FROM
                INFORMATION_SCHEMA.COLUMNS
            WHERE
                TABLE_SCHEMA = 'dbo' AND
                TABLE_NAME = '$table'
            ORDER BY
                ORDINAL_POSITION ASC";
        $table_struktur = DB::select(DB::raw($sql));

        if(count($table_struktur) == 0){
            $this->tableCreate($table, $field);
            return "create";
        }else{


            // ADD
            if($request->add && count($request->add) != 0){
                foreach ($request->add as $add) {
                    $column_name = $add['column_name'];
                    $data_type = $add['data_type'];
                    $data_length = $add['data_length'];

                    $query = "ALTER TABLE {$table} ADD {$column_name} {$data_type} ({$data_length})";
                    $Qadd = DB::statement($query);
                }
            }

            // EDIT
            if($request->edit_rename && count($request->edit_rename) != 0){
                foreach ($request->edit_rename as $edit_rename) {
                    $column_name_original = $edit_rename['column_name_original'];
                    $column_name = $edit_rename['column_name'];

                    $query = "EXEC sp_RENAME '{$table}.{$column_name_original}', '{$column_name}', 'COLUMN'";
                    $Qrename = DB::statement($query);
                }
            }

            // DROP
            if($request->drop && count($request->drop) != 0){
                foreach ($request->drop as $drop) {
                    $column_name = $drop['column_name'];

                    $query = "ALTER TABLE {$table} DROP COLUMN {$column_name}";
                    $Qdrop = DB::statement($query);
                }
            }

            // EDIT
            if($request->edit_type && count($request->edit_type) != 0){
                foreach ($request->edit_type as $edit_type) {
                    $column_name = $edit_type['column_name'];
                    $data_type = $edit_type['data_type'];

                    $query = "ALTER TABLE {$table} ALTER COLUMN {$column_name} {$data_type}";
                    $Qdrop = DB::statement($query);
                }
            }
        }
    }

    public function tableCreate($table, $field)
    {
        for ($i=0; $i < count($field) ; $i++) {
            if($field[$i]['column_name'] == "" OR $field[$i]['column_name'] === null){
                continue;
            }

            $column_name = $field[$i]['column_name'];
            $data_type   = $field[$i]['data_type'];
            $data_length = $field[$i]['data_length'] ? ("(".$field[$i]['data_length'].")") : "";

            $column[$i] = $column_name." ".$data_type.$data_length;
        }

        $query = implode(", ", $column);
        $query = "CREATE TABLE $table ($query)";

        $menu = DB::table("auth.menu")->where('kode', str_replace('_', '-', $table))->where('type_menu', 'manajemen')->first();
        $insert = [
            "module_id"   => DB::raw("NEWID()"), 
            "table"       => $table,
            "menu_id"     => $menu->menu_id,
            "type_module" => "list",
            "soft_delete" => 0,
            "create_date" => DB::raw("GETDATE()"),
            "last_update" => DB::raw("GETDATE()")
        ];

        $insert = DB::table("manajemen_module")->insert($insert);
        $create = DB::statement($query);
    }
}
