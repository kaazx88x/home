<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Addcolumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_product_attributes', function (Blueprint $table) {
            $table->string('attribute_cnt')->after('attribute_item_cn')->nullable();
            $table->string('attribute_item_cnt')->after('attribute_cnt')->nullable();
        });
        //
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nm_product_attributes', function (Blueprint $table) {
            $table->dropColumn('attribute_cnt');
            $table->dropColumn('attribute_item_cnt');
        });
        //
    }
}
