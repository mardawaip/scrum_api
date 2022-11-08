<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('menu_id');
            $table->string('kode', 50)->nullable();
            $table->string('title', 100)->nullable();
            $table->string('type', 50)->nullable();
            $table->string('icon', 50)->nullable();
            $table->string('auth', 50)->nullable();
            $table->string('url', 255)->nullable();
            $table->integer('induk_menu_id')->nullable();
            $table->integer('nomor_urut')->nullable();
            $table->integer('tingkat_menu')->nullable();
            $table->integer('soft_delete')->nullable();
            $table->integer('dashboard')->nullable();
            $table->string('dashboard_icon', 100)->nullable();
            $table->string('dashboard_icon_hover', 100)->nullable();
            $table->string('type_menu', 50)->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu');
    }
};
