<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCustomerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_customer', function (Blueprint $table) {
            $table->string('identity_card', 50)->nullable(true)->after('cus_name');
            $table->smallInteger('update_flag')->nullable(true)->after('account_locked')->default(1);
        });

        DB::table('nm_customer')->update(['update_flag' => 0]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nm_customer', function (Blueprint $table) {
            $table->dropColumn('identity_card');
            $table->dropColumn('update_flag');
        });
    }
}
