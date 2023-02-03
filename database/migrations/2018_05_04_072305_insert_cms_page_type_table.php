<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Carbon\Carbon;

class InsertCmsPageTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('cms_page_type')->insert([
            'id' => 1,
            'type' => 'web',
            'name' => 'Web CMS Pages',
            'description' => null,
            'created_at' => Carbon::now('UTC'),
            'updated_at' => Carbon::now('UTC')
        ]);

        DB::table('cms_page_type')->insert([
            'id' => 2,
            'type' => 'mobile',
            'name' => 'Mobile - Ecard FAQ',
            'description' => null,
            'created_at' => Carbon::now('UTC'),
            'updated_at' => Carbon::now('UTC')
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
