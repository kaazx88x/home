<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpReportMonthlySaleByDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
			CREATE  PROCEDURE `sp_report_month_sale_by_date`()
			BEGIN

			DECLARE today_date DATE;
			DECLARE first_day_of_last_month DATE;
			DECLARE first_day_of_this_month DATE;
			DECLARE last_month INTEGER;
			DECLARE year_of_last_month INTEGER;

			SET today_date = date(DATE_SUB(DATE_ADD(now(), INTERVAL 8 HOUR), INTERVAL 1 DAY));

			SET first_day_of_last_month = concat(date_format(LAST_DAY(today_date - interval 1 month),'%Y-%m-'),'01'); 
			SET first_day_of_this_month = concat(date_format(LAST_DAY(today_date ),'%Y-%m-'),'01'); 

			SET last_month = date_format(LAST_DAY(today_date - interval 1 month),'%m'); 
			SET year_of_last_month = date_format(LAST_DAY(today_date - interval 1 month),'%Y'); 

			DELETE FROM report_monthly_sale_by_date WHERE year = year_of_last_month AND month = last_month ;


			/* ONLINE */
			/* STATUS 1 = SUCCESS */
			INSERT report_monthly_sale_by_date (year, month, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT year_of_last_month, last_month, 1, merchant_id, 1, store_id, sum(total_order), sum(total_product), sum(sales_amount),  sum(platform_charge),  sum(customer_charge), sum(merchant_commission), NOW(), NOW()
			FROM report_daily_sale_by_date
			WHERE transaction_date >= first_day_of_last_month
			AND transaction_date < first_day_of_this_month
			AND order_status IN(1)
			AND online_offline_status = 1
			GROUP BY merchant_id, store_id;

			/* STATUS 2 = CANCEL */
			INSERT report_monthly_sale_by_date (year, month, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT year_of_last_month, last_month, 2, merchant_id, 1, store_id, sum(total_order), sum(total_product), sum(sales_amount),  sum(platform_charge),  sum(customer_charge), sum(merchant_commission), NOW(), NOW()
			FROM report_daily_sale_by_date
			WHERE transaction_date >= first_day_of_last_month
			AND transaction_date < first_day_of_this_month
			AND order_status IN(2)
			AND online_offline_status = 1
			GROUP BY merchant_id, store_id;

			/* STATUS 3 = REFUNDED */
			INSERT report_monthly_sale_by_date (year, month, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT year_of_last_month, last_month, 3, merchant_id, 1, store_id, sum(total_order), sum(total_product), sum(sales_amount),  sum(platform_charge),  sum(customer_charge), sum(merchant_commission), NOW(), NOW()
			FROM report_daily_sale_by_date
			WHERE transaction_date >= first_day_of_last_month
			AND transaction_date < first_day_of_this_month
			AND order_status IN(3)
			AND online_offline_status = 1
			GROUP BY merchant_id, store_id;


			/* OFFLINE */
			/* STATUS 1 = SUCCESS */
			INSERT report_monthly_sale_by_date (year, month, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT year_of_last_month, last_month, 1, merchant_id, 2, store_id, sum(total_order), sum(total_product), sum(sales_amount),  sum(platform_charge),  sum(customer_charge), sum(merchant_commission), NOW(), NOW()
			FROM report_daily_sale_by_date
			WHERE transaction_date >= first_day_of_last_month
			AND transaction_date < first_day_of_this_month
			AND order_status IN(1)
			AND online_offline_status = 2
			GROUP BY merchant_id, store_id;

			/* STATUS 2 = CANCEL */
			INSERT report_monthly_sale_by_date (year, month, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT year_of_last_month, last_month, 2, merchant_id, 2, store_id, sum(total_order), sum(total_product), sum(sales_amount),  sum(platform_charge),  sum(customer_charge), sum(merchant_commission), NOW(), NOW()
			FROM report_daily_sale_by_date
			WHERE transaction_date >= first_day_of_last_month
			AND transaction_date < first_day_of_this_month
			AND order_status IN(2)
			AND online_offline_status = 2
			GROUP BY merchant_id, store_id;

			/* STATUS 3 = REFUNDED */
			INSERT report_monthly_sale_by_date (year, month, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT year_of_last_month, last_month, 3, merchant_id, 2, store_id, sum(total_order), sum(total_product), sum(sales_amount),  sum(platform_charge),  sum(customer_charge), sum(merchant_commission), NOW(), NOW()
			FROM report_daily_sale_by_date
			WHERE transaction_date >= first_day_of_last_month
			AND transaction_date < first_day_of_this_month
			AND order_status IN(3)
			AND online_offline_status = 2
			GROUP BY merchant_id, store_id;


			select today_date;

			END
		
		");
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
