<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertPermissionRoleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permission_role')->insert([
            'permission_id' => 1,
            'role_id' => 1
        ]);

        DB::table('permission_role')->insert([
            'permission_id' => 2,
            'role_id' => 1
        ]);

        DB::table('permission_role')->insert([
            'permission_id' => 3,
            'role_id' => 1
        ]);

        DB::table('permission_role')->insert([
            'permission_id' => 101,
            'role_id' => 1
        ]);

        DB::table('permission_role')->insert([
            'permission_id' => 102,
            'role_id' => 1
        ]);

        DB::table('permission_role')->insert([
            'permission_id' => 103,
            'role_id' => 1
        ]);

        DB::table('permission_role')->insert([
            'permission_id' => 104,
            'role_id' => 1
        ]);

        DB::table('permission_role')->insert([
            'permission_id' => 105,
            'role_id' => 1
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
