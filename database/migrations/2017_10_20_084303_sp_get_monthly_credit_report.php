<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpGetMonthlyCreditReport extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
			CREATE PROCEDURE `sp_get_monthly_credit_report`(
						user_type_input varchar(20),
						credit_type_input int,
						month_from int,
						year_from int,
						month_to int,
						year_to int,
						country_list nvarchar(200),
						sort_by nvarchar(200)
			)
			BEGIN
				SELECT * FROM 
				(SELECT year as l1_year,
						month as l1_month,
						user_type as l1_user_type,
						credit_type as l1_credit_type,
						country_id as l1_country_id,
						co_name as l1_country_name,
						SUM(credit_amount) as l1_credit_amount,
						SUM(debit_amount) as l1_debit_amount
				FROM report_monthly_credit
				LEFT JOIN nm_country ON(report_monthly_credit.country_id = nm_country.co_id)
				WHERE report_monthly_credit.year >= year_from
				AND  report_monthly_credit.year <= year_to
				AND  report_monthly_credit.month >= month_from
				AND  report_monthly_credit.month <= month_to
				AND user_type = user_type_input
				AND credit_type = credit_type_input
				AND FIND_IN_SET(`report_monthly_credit`.`country_id`, country_list)
				GROUP BY l1_year, l1_month, l1_user_type, l1_credit_type ) as table1
				
				INNER JOIN 
				
				(SELECT year as l2_year,
						month as l2_month,
						user_type as l2_user_type,
						credit_type as l2_credit_type,
						country_id as l2_country_id,
						co_name as l2_country_name,
						SUM(credit_amount) as l2_credit_amount,
						SUM(debit_amount) as l2_debit_amount
				FROM report_monthly_credit
				LEFT JOIN nm_country ON(report_monthly_credit.country_id = nm_country.co_id)
				WHERE report_monthly_credit.year >= year_from
				AND  report_monthly_credit.year <= year_to
				AND  report_monthly_credit.month >= month_from
				AND  report_monthly_credit.month <= month_to
				AND user_type = user_type_input
				AND credit_type = credit_type_input
				AND FIND_IN_SET(`report_monthly_credit`.`country_id`, country_list)
				GROUP BY l2_year, l2_month, l2_user_type, l2_credit_type, l2_country_id ) as table2
				
				ON (table1.l1_year = table2.l2_year AND table1.l1_month = table2.l2_month)
				
				ORDER BY
				
				CASE WHEN sort_by = 'trans_date_asc' THEN `l1_year` END ASC, 
				CASE WHEN sort_by = 'trans_date_asc' THEN `l2_year` END ASC, 
				CASE WHEN sort_by = 'trans_date_asc' THEN `l1_month` END ASC, 
				CASE WHEN sort_by = 'trans_date_asc' THEN `l2_month` END ASC, 
				
				CASE WHEN sort_by = 'trans_date_asc' THEN `l1_year` END DESC, 
				CASE WHEN sort_by = 'trans_date_asc' THEN `l2_year` END DESC, 
				CASE WHEN sort_by = 'trans_date_asc' THEN `l1_month` END DESC, 
				CASE WHEN sort_by = 'trans_date_asc' THEN `l2_month` END DESC, 
				
				CASE WHEN sort_by = 'credit_asc' THEN `l1_credit_amount` END ASC, 
				CASE WHEN sort_by = 'credit_asc' THEN `l2_credit_amount` END ASC, 
				
				CASE WHEN sort_by = 'credit_desc' THEN `l1_credit_amount` END DESC, 
				CASE WHEN sort_by = 'credit_desc' THEN `l2_credit_amount` END DESC, 
				
				CASE WHEN sort_by = 'debit_asc' THEN `l1_debit_amount` END ASC, 
				CASE WHEN sort_by = 'debit_asc' THEN `l2_debit_amount` END ASC, 
				
				CASE WHEN sort_by = 'debit_desc' THEN `l1_debit_amount` END DESC, 
				CASE WHEN sort_by = 'debit_desc' THEN `l2_debit_amount` END DESC
				;

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
