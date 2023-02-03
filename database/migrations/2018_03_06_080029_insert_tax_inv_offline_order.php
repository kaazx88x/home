<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertTaxInvOfflineOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::table('order_offline', function (Blueprint $table) {
            $table->string('tax_inv_no')->after('inv_no');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
         Schema::table('order_offline', function (Blueprint $table) {
            $table->dropColumn(['tax_inv_no']);
        });
    }
}
