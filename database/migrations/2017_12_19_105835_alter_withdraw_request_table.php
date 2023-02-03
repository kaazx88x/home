<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterWithdrawRequestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE `nm_withdraw_request` ADD COLUMN `wd_statement` VARCHAR(50) NULL DEFAULT NULL AFTER `wd_rate`;");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("ALTER TABLE `nm_withdraw_request` DROP COLUMN `wd_statement`;");
    }
}
