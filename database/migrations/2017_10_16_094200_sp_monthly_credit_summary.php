<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpMonthlyCreditSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
			CREATE  PROCEDURE `sp_report_monthly_credit`()
			BEGIN

				DECLARE today_date DATE;
				DECLARE first_day_of_last_month DATE;
				DECLARE first_day_of_this_month DATE;
				DECLARE last_month INTEGER;
				DECLARE year_of_last_month INTEGER;
				
				/* Today Date */
				SET today_date = date(DATE_SUB(DATE_ADD(NOW(), INTERVAL 8 HOUR), INTERVAL 1 DAY));
				
				SET first_day_of_last_month = concat(date_format(LAST_DAY(today_date - interval 1 month),'%Y-%m-'),'01'); 
				SET first_day_of_this_month = concat(date_format(LAST_DAY(today_date ),'%Y-%m-'),'01'); 

				SET last_month = date_format(LAST_DAY(today_date - interval 1 month),'%m'); 
				SET year_of_last_month = date_format(LAST_DAY(today_date - interval 1 month),'%Y'); 

				DELETE FROM report_monthly_credit WHERE year = year_of_last_month AND month = last_month;
				
				
				INSERT report_monthly_credit(year, month, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT year_of_last_month, last_month, MAX(user_type), MAX(credit_type), MAX(country_id), SUM(credit_amount), SUM(debit_amount), NOW(), NOW()
				FROM report_daily_credit
				LEFT JOIN nm_country ON(nm_country.co_id = report_daily_credit.country_id)
				WHERE date >= first_day_of_last_month
				AND date < first_day_of_this_month
				GROUP BY  user_type, credit_type , country_id;
				
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
