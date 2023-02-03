<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateNmStore extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_store', function (Blueprint $table) {
            $table->smallInteger('backup_accept_payment')->after('accept_payment')->nullable()->default('0');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nm_store', function (Blueprint $table) {
            $table->dropColumn(['backup_accept_payment']);
        });
    }
}
