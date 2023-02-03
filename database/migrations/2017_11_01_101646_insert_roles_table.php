<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('roles')->insert([
            'name' => 'Root Account',
            'display_name' => 'Root Account',
            'description' => '- can do everything',
            'role_level' => 1
        ]);

        DB::table('roles')->insert([
            'name' => 'Super User',
            'display_name' => 'Super User',
            'description' => '- can do everything
            - for role access setting, only can view
            - allow them to unlock/lock admin account',
            'role_level' => 2
        ]);

        DB::table('roles')->insert([
            'name' => 'General Manager',
            'display_name' => 'General Manager',
            'description' => '- assign to single or more than one country
            - can access everything like super user, but data is by assigned country, also cannot credit/debit member and merchant credit
            - can add admin, role below GM',
            'role_level' => 3
        ]);

        DB::table('roles')->insert([
            'name' => 'Head of Operation',
            'display_name' => 'Head of Operation',
            'description' => '- assign to single country
            - cannot add admin user',
            'role_level' => 4
        ]);

        DB::table('roles')->insert([
            'name' => 'Operation',
            'display_name' => 'Operation',
            'description' => '- assign to single or more than one country
            - can access everything like country manager, except:
            - can not update merchant bank info
            - can not update merchant login id and email
            - can not update merchant charges value
            - can not update customer email',
            'role_level' => 5
        ]);

        DB::table('roles')->insert([
            'name' => 'Customer Service',
            'display_name' => 'Customer Service',
            'description' => '- assign to single or more than one country
            - can access everything like operation, except:
            - can not update merchant profile (everything), view only
            - can not update member profile (everything), view only
            - can not export',
            'role_level' => 6
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