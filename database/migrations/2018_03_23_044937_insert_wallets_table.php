<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertWalletsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('wallets')
        ->where('name_en', 'Special')
        ->update([
            'name_en' => 'AP'
        ]);

        DB::table('wallets')
        ->insert([
            'name_en' => 'AP-2',
            'name_cn' => '',
            'name_cnt' => '',
            'percentage' => 0
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('wallets')
        ->where('name_en', 'AP-2')
        ->delete();
    }
}
