<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerTableLimitModule extends Migration
{
    public function up()
    {
        Schema::table('nm_customer', function (Blueprint $table) {
            $table->integer('limit_id')->after('cus_status')->default(0);
        });
    }

    public function down()
    {
        Schema::table('nm_customer', function (Blueprint $table) {
            if (Schema::hasColumn('nm_customer', 'limit_id'))
                $table->dropColumn('limit_id');
        });
    }
}
