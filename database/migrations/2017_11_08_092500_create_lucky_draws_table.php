<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLuckyDrawsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lucky_draws', function (Blueprint $table) {
            $table->increments('id');
            $table->smallInteger('type')->nullable(false)->default(0);
            $table->integer('value')->nullable(true)->default(0);
            $table->text('description_en')->nullable(false);
            $table->text('description_cn')->nullable(true);
            $table->text('description_cnt')->nullable(true);
            $table->string('code', 20)->nullable(false);
            $table->BigInteger('claim_by')->nullable(true);
            $table->dateTime('claim_date')->nullable(true);
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('lucky_draws');
    }
}
