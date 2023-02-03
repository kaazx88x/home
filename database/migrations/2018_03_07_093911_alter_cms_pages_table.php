<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCmsPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('nm_cms_pages', function (Blueprint $table) {
            $table->dropColumn(['cp_title_my', 'cp_description_my']);
            $table->smallInteger('cp_cms_type')->after('cp_id')->nullable(false)->default('1');
            $table->string('cp_title_cnt', 250)->after('cp_title_cn')->nullable();
            $table->longText('cp_description_cnt')->after('cp_description_cn')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('nm_cms_pages', function (Blueprint $table) {
            $table->dropColumn(['cp_cms_type', 'cp_title_cnt', 'cp_description_cnt']);
        });
    }
}
