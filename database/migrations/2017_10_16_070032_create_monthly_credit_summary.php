<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMonthlyCreditSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_monthly_credit', function (Blueprint $table) {
            $table->increments('id');
			$table->integer('year');
			$table->integer('month');
			$table->string('user_type')->nullable(false)->default(0);
			$table->integer('credit_type')->nullable(false)->default(0);
			$table->integer('country_id')->nullable(false)->default(0);
			$table->decimal('credit_amount', 11,4)->nullable(false)->default(0.00);
			$table->decimal('debit_amount', 11,4)->nullable(false)->default(0.00);
			$table->timestamps();
        });
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
