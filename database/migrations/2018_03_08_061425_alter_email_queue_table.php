<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEmailQueueTable extends Migration
{
    public function up()
    {
        Schema::table('nm_email_queues', function (Blueprint $table) {

            if (Schema::hasColumn('nm_email_queues', 'table'))
                $table->dropColumn('table');

            if (Schema::hasColumn('nm_email_queues', 'refference_id'))
                $table->dropColumn('refference_id');
            
            if (Schema::hasColumn('nm_email_queues', 'remarks'))
                $table->dropColumn('remarks');
            
            $table->string('type')->after('jobs');
            $table->string('notifiable_id')->after('type');
            $table->string('notifiable_type')->after('notifiable_id')->default(0);
            $table->text('data')->after('notifiable_type');
        });

        Schema::table('nm_email_queues', function (Blueprint $table) {            
            $table->text('remarks')->after('send');
        });
    }

    public function down()
    {
        Schema::table('nm_email_queues', function (Blueprint $table) {

            if (!Schema::hasColumn('nm_email_queues', 'table'))
                $table->string('table')->after('jobs');

            if (!Schema::hasColumn('nm_email_queues', 'refference_id'))
                $table->integer('refference_id')->after('jobs')->default(0);

            if (Schema::hasColumn('nm_email_queues', 'type'))
                $table->dropColumn('type');

            if (Schema::hasColumn('nm_email_queues', 'notifiable_id'))
                $table->dropColumn('notifiable_id');

            if (Schema::hasColumn('nm_email_queues', 'notifiable_type'))
                $table->dropColumn('notifiable_type');

            if (Schema::hasColumn('nm_email_queues', 'data'))
                $table->dropColumn('data');
        });
    }
}
