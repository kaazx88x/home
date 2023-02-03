<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDailySalesByTransactionDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('report_daily_sale_by_date', function (Blueprint $table) {
            $table->increments('id');
			$table->date('transaction_date');
			$table->integer('order_status')->nullable(false)->default(0);
			$table->integer('merchant_id')->nullable(false)->default(0);
			$table->integer('online_offline_status')->nullable(false)->default(0);
			$table->integer('store_id')->nullable(false)->default(0);
			$table->integer('total_order')->nullable(false)->default(0);
			$table->integer('total_product')->nullable(false)->default(0);
			$table->decimal('sales_amount', 11,4)->nullable(false)->default(0.00);
			$table->decimal('platform_charge', 11,4)->nullable(false)->default(0.00);
			$table->decimal('customer_charge', 11,4)->nullable(false)->default(0.00);
			$table->decimal('merchant_commission', 11,4)->nullable(false)->default(0.00);
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
