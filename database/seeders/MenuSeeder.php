<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use DB;
use Illuminate\Support\Str;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu')->insert([
            'menu_id' => '1',
            'kode' => Str::random(10),
            'kode' => Str::random(10),
            'title' => Str::random(10),
            'type' => Str::random(10),
            'icon' => Str::random(10),
            'auth' => Str::random(10),
            'url' => Str::random(10),
            'induk_menu_id' => Str::random(10),
            'nomor_urut' => Str::random(10),
            'tingkat_menu' => Str::random(10),
            'soft_delete' => Str::random(10),
            'dashboard' => Str::random(10),
            'dashboard_icon' => Str::random(10),
            'dashboard_icon_hover' => Str::random(10),
            'type_menu' => Str::random(10),
            'status' => Str::random(10),
        ]);

    }
}
