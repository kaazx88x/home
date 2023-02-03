<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterProductTableLimitModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_product', function (Blueprint $table) {
            $table->smallInteger('limit_enabled')->after('end_date')->default('0');
            $table->smallInteger('limit_type')->after('limit_enabled')->default('0')->comment('0 => Single Purchase, 1 => Daily, 2 => Weekly, 3 => Monthly, 4 => Yearly');
            $table->integer('limit_quantity')->after('limit_type')->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $table->dropColumn('limit_enabled');
        $table->dropColumn('limit_type');
        $table->dropColumn('limit_quantity');
    }
}
