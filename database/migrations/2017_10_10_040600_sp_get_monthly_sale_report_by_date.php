<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpGetMonthlySaleReportByDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
			CREATE PROCEDURE `sp_get_monthly_sale_report_by_date`(
				transaction_type int,
				month_from int,
                year_from int,
                month_to int,
                year_to int,
				orderstatus int, 
				merchant_country_list nvarchar(200),
				store_country_list nvarchar(200),
				sort_by nvarchar(200)
			)
			BEGIN

				SELECT * FROM 
					(select 
						report_monthly_sale_by_date.month as l1_month, 
						report_monthly_sale_by_date.year as l1_year, 
						nm_merchant.mer_id as l1_merchant_id, 
						nm_merchant.mer_fname as l1_merchant_name1, 
						nm_merchant.mer_lname as l1_merchant_name2, 
						nm_country.co_id as l1_merchant_country_id, 
						nm_country.co_name as l1_merchant_country, 
						report_monthly_sale_by_date.online_offline_status as l1_online_offline_status, 
						report_monthly_sale_by_date.store_id as l1_store_id, 
						nm_store.stor_name as l1_stor_name, 
						(SELECT co_name FROM nm_country WHERE co_id = nm_store.stor_country LIMIT 1 ) as l1_store_country, 
						SUM(report_monthly_sale_by_date.total_order) as l1_total_order, 
						SUM(report_monthly_sale_by_date.total_product) as l1_total_product, 
						SUM(report_monthly_sale_by_date.sales_amount) as l1_sales_amount, 
						SUM(report_monthly_sale_by_date.platform_charge) as l1_platform_charge, 
						SUM(report_monthly_sale_by_date.customer_charge) as l1_customer_charge,
						SUM(report_monthly_sale_by_date.merchant_commission) as l1_merchant_commission, 
						SUM(sales_amount - platform_charge - customer_charge - merchant_commission) as l1_merchant_earning, 
						SUM(platform_charge + customer_charge + merchant_commission) as l1_earning
						
					from `report_monthly_sale_by_date` 
					inner join `nm_merchant` on `nm_merchant`.`mer_id` = `report_monthly_sale_by_date`.`merchant_id` 
					inner join `nm_store` on `nm_store`.`stor_id` = `report_monthly_sale_by_date`.`store_id` 
					inner join `nm_country` on `nm_merchant`.`mer_co_id` = `nm_country`.`co_id` 
					where `online_offline_status` = transaction_type
					and `year` >= year_from
					and `year` <= year_to 
					and `month` >= month_from
					and `month` <= month_to 
					and `order_status` = orderstatus
					and FIND_IN_SET(`nm_merchant`.`mer_co_id`, merchant_country_list)
					and FIND_IN_SET(`nm_store`.`stor_country`, store_country_list)
					group by `l1_year`, `l1_month`  ) as table1

					INNER JOIN 

					(select 
						report_monthly_sale_by_date.month as l2_month, 
						report_monthly_sale_by_date.year as l2_year, 
						nm_merchant.mer_id as l2_merchant_id, 
						nm_merchant.mer_fname as l2_merchant_name1, 
						nm_merchant.mer_lname as l2_merchant_name2, 
						nm_country.co_id as l2_merchant_country_id, 
						nm_country.co_name as l2_merchant_country, 
						report_monthly_sale_by_date.online_offline_status as l2_online_offline_status, 
						report_monthly_sale_by_date.store_id as l2_store_id, 
						nm_store.stor_name as l2_stor_name, 
						(SELECT co_name FROM nm_country WHERE co_id = nm_store.stor_country LIMIT 1 ) as l2_store_country, 
						SUM(report_monthly_sale_by_date.total_order) as l2_total_order, 
						SUM(report_monthly_sale_by_date.total_product) as l2_total_product, 
						SUM(report_monthly_sale_by_date.sales_amount) as l2_sales_amount, 
						SUM(report_monthly_sale_by_date.platform_charge) as l2_platform_charge, 
						SUM(report_monthly_sale_by_date.customer_charge) as l2_customer_charge,
						SUM(report_monthly_sale_by_date.merchant_commission) as l2_merchant_commission, 
						SUM(sales_amount - platform_charge - customer_charge - merchant_commission) as l2_merchant_earning, 
						SUM(platform_charge + customer_charge + merchant_commission) as l2_earning
						
					from `report_monthly_sale_by_date` 
					inner join `nm_merchant` on `nm_merchant`.`mer_id` = `report_monthly_sale_by_date`.`merchant_id` 
					inner join `nm_store` on `nm_store`.`stor_id` = `report_monthly_sale_by_date`.`store_id` 
					inner join `nm_country` on `nm_merchant`.`mer_co_id` = `nm_country`.`co_id` 
					where `online_offline_status` = transaction_type 
					and `year` >= year_from
					and `year` <= year_to 
					and `month` >= month_from
					and `month` <= month_to 
					and `order_status` = orderstatus
					and FIND_IN_SET(`nm_merchant`.`mer_co_id`, merchant_country_list)
					and FIND_IN_SET(`nm_store`.`stor_country`, store_country_list)
					group by `l2_year`, `l2_month`, `l2_merchant_country_id` ) as table2

					ON (table1.l1_year = table2.l2_year AND table1.l1_month = table2.l2_month)

					INNER JOIN

					(select 
						report_monthly_sale_by_date.month as l3_month, 
						report_monthly_sale_by_date.year as l3_year, 
						nm_merchant.mer_id as l3_merchant_id, 
						nm_merchant.mer_fname as l3_merchant_name1, 
						nm_merchant.mer_lname as l3_merchant_name2, 
						nm_country.co_id as l3_merchant_country_id, 
						nm_country.co_name as l3_merchant_country, 
						report_monthly_sale_by_date.online_offline_status as l3_online_offline_status, 
						report_monthly_sale_by_date.store_id as l3_store_id, 
						nm_store.stor_name as l3_stor_name, 
						(SELECT co_name FROM nm_country WHERE co_id = nm_store.stor_country LIMIT 1 ) as l3_store_country, 
						SUM(report_monthly_sale_by_date.total_order) as l3_total_order, 
						SUM(report_monthly_sale_by_date.total_product) as l3_total_product, 
						SUM(report_monthly_sale_by_date.sales_amount) as l3_sales_amount, 
						SUM(report_monthly_sale_by_date.platform_charge) as l3_platform_charge, 
						SUM(report_monthly_sale_by_date.customer_charge) as l3_customer_charge,
						SUM(report_monthly_sale_by_date.merchant_commission) as l3_merchant_commission, 
						SUM(sales_amount - platform_charge - customer_charge - merchant_commission) as l3_merchant_earning, 
						SUM(platform_charge + customer_charge + merchant_commission) as l3_earning
						
					from `report_monthly_sale_by_date` 
					inner join `nm_merchant` on `nm_merchant`.`mer_id` = `report_monthly_sale_by_date`.`merchant_id` 
					inner join `nm_store` on `nm_store`.`stor_id` = `report_monthly_sale_by_date`.`store_id` 
					inner join `nm_country` on `nm_merchant`.`mer_co_id` = `nm_country`.`co_id` 
					where `online_offline_status` = transaction_type 
					and `year` >= year_from
					and `year` <= year_to 
					and `month` >= month_from
					and `month` <= month_to 
					and `order_status` = orderstatus
					and FIND_IN_SET(`nm_merchant`.`mer_co_id`, merchant_country_list)
					and FIND_IN_SET(`nm_store`.`stor_country`, store_country_list)
					group by `l3_year`, `l3_month`, `l3_merchant_country_id`, `l3_merchant_id`  ) as table3

					ON (table2.l2_year = table3.l3_year AND table2.l2_month = table3.l3_month AND table2.l2_merchant_country_id = table3.l3_merchant_country_id)

					INNER JOIN

					(select 
						report_monthly_sale_by_date.month as l4_month, 
						report_monthly_sale_by_date.year as l4_year, 
						nm_merchant.mer_id as l4_merchant_id, 
						nm_merchant.mer_fname as l4_merchant_name1, 
						nm_merchant.mer_lname as l4_merchant_name2, 
						nm_country.co_id as l4_merchant_country_id, 
						nm_country.co_name as l4_merchant_country, 
						report_monthly_sale_by_date.online_offline_status as l4_online_offline_status, 
						report_monthly_sale_by_date.store_id as l4_store_id, 
						nm_store.stor_name as l4_stor_name, 
						(SELECT co_name FROM nm_country WHERE co_id = nm_store.stor_country LIMIT 1 ) as l4_store_country, 
						SUM(report_monthly_sale_by_date.total_order) as l4_total_order, 
						SUM(report_monthly_sale_by_date.total_product) as l4_total_product, 
						SUM(report_monthly_sale_by_date.sales_amount) as l4_sales_amount, 
						SUM(report_monthly_sale_by_date.platform_charge) as l4_platform_charge, 
						SUM(report_monthly_sale_by_date.customer_charge) as l4_customer_charge,
						SUM(report_monthly_sale_by_date.merchant_commission) as l4_merchant_commission, 
						SUM(sales_amount - platform_charge - customer_charge - merchant_commission) as l4_merchant_earning, 
						SUM(platform_charge + customer_charge + merchant_commission) as l4_earning
						
					from `report_monthly_sale_by_date` 
					inner join `nm_merchant` on `nm_merchant`.`mer_id` = `report_monthly_sale_by_date`.`merchant_id` 
					inner join `nm_store` on `nm_store`.`stor_id` = `report_monthly_sale_by_date`.`store_id` 
					inner join `nm_country` on `nm_merchant`.`mer_co_id` = `nm_country`.`co_id` 
					where `online_offline_status` = transaction_type 
					and `year` >= year_from
					and `year` <= year_to 
					and `month` >= month_from
					and `month` <= month_to 
					and `order_status` = orderstatus
					and FIND_IN_SET(`nm_merchant`.`mer_co_id`, merchant_country_list)
					and FIND_IN_SET(`nm_store`.`stor_country`, store_country_list)
					group by `l4_year`, `l4_month`, `l4_merchant_country_id`, `l4_merchant_id`, `l4_store_id`  ) as table4

					ON (table3.l3_year = table4.l4_year AND table3.l3_month = table4.l4_month AND table3.l3_merchant_country_id = table4.l4_merchant_country_id AND table3.l3_merchant_id = table4.l4_merchant_id)

					order by 

					CASE WHEN sort_by = 'trans_date_asc' THEN `l1_year` END ASC, 
					CASE WHEN sort_by = 'trans_date_asc' THEN `l1_month` END ASC, 
					CASE WHEN sort_by = 'trans_date_asc' THEN `l2_year` END ASC, 
					CASE WHEN sort_by = 'trans_date_asc' THEN `l2_month` END ASC, 
					CASE WHEN sort_by = 'trans_date_asc' THEN `l3_year` END ASC, 
					CASE WHEN sort_by = 'trans_date_asc' THEN `l3_month` END ASC, 
					CASE WHEN sort_by = 'trans_date_asc' THEN `l4_year` END ASC, 
					CASE WHEN sort_by = 'trans_date_asc' THEN `l4_month` END ASC, 

					CASE WHEN sort_by = 'trans_date_desc' THEN `l1_year` END DESC,
					CASE WHEN sort_by = 'trans_date_desc' THEN `l1_month` END DESC,
					CASE WHEN sort_by = 'trans_date_desc' THEN `l2_year` END DESC,
					CASE WHEN sort_by = 'trans_date_desc' THEN `l2_month` END DESC,
					CASE WHEN sort_by = 'trans_date_desc' THEN `l3_year` END DESC,
					CASE WHEN sort_by = 'trans_date_desc' THEN `l3_month` END DESC,
					CASE WHEN sort_by = 'trans_date_desc' THEN `l4_year` END DESC,
					CASE WHEN sort_by = 'trans_date_desc' THEN `l4_month` END DESC,

					CASE WHEN sort_by = 'totalsale_asc' THEN `l1_sales_amount` END ASC,
					CASE WHEN sort_by = 'totalsale_asc' THEN `l2_sales_amount` END ASC,
					CASE WHEN sort_by = 'totalsale_asc' THEN `l3_sales_amount` END ASC,
					CASE WHEN sort_by = 'totalsale_asc' THEN `l4_sales_amount` END ASC,

					CASE WHEN sort_by = 'totalsale_desc' THEN `l1_sales_amount` END DESC,
					CASE WHEN sort_by = 'totalsale_desc' THEN `l2_sales_amount` END DESC,
					CASE WHEN sort_by = 'totalsale_desc' THEN `l3_sales_amount` END DESC,
					CASE WHEN sort_by = 'totalsale_desc' THEN `l4_sales_amount` END DESC,

					CASE WHEN sort_by = 'platformcharge_asc' THEN `l1_platform_charge` END ASC,
					CASE WHEN sort_by = 'platformcharge_asc' THEN `l2_platform_charge` END ASC,
					CASE WHEN sort_by = 'platformcharge_asc' THEN `l3_platform_charge` END ASC,
					CASE WHEN sort_by = 'platformcharge_asc' THEN `l4_platform_charge` END ASC,

					CASE WHEN sort_by = 'platformcharge_desc' THEN `l1_platform_charge` END DESC,
					CASE WHEN sort_by = 'platformcharge_desc' THEN `l2_platform_charge` END DESC,
					CASE WHEN sort_by = 'platformcharge_desc' THEN `l3_platform_charge` END DESC,
					CASE WHEN sort_by = 'platformcharge_desc' THEN `l4_platform_charge` END DESC,

					CASE WHEN sort_by = 'customercharge_asc' THEN `l1_customer_charge` END ASC,
					CASE WHEN sort_by = 'customercharge_asc' THEN `l2_customer_charge` END ASC,
					CASE WHEN sort_by = 'customercharge_asc' THEN `l3_customer_charge` END ASC,
					CASE WHEN sort_by = 'customercharge_asc' THEN `l4_customer_charge` END ASC,

					CASE WHEN sort_by = 'customercharge_desc' THEN `l1_customer_charge` END DESC,
					CASE WHEN sort_by = 'customercharge_desc' THEN `l2_customer_charge` END DESC,
					CASE WHEN sort_by = 'customercharge_desc' THEN `l3_customer_charge` END DESC,
					CASE WHEN sort_by = 'customercharge_desc' THEN `l4_customer_charge` END DESC,

					CASE WHEN sort_by = 'merchant_charge_asc' THEN `l1_customer_charge` END ASC,
					CASE WHEN sort_by = 'merchant_charge_asc' THEN `l2_customer_charge` END ASC,
					CASE WHEN sort_by = 'merchant_charge_asc' THEN `l3_customer_charge` END ASC,
					CASE WHEN sort_by = 'merchant_charge_asc' THEN `l4_customer_charge` END ASC,

					CASE WHEN sort_by = 'merchant_charge_desc' THEN `l1_customer_charge` END DESC,
					CASE WHEN sort_by = 'merchant_charge_desc' THEN `l2_customer_charge` END DESC,
					CASE WHEN sort_by = 'merchant_charge_desc' THEN `l3_customer_charge` END DESC,
					CASE WHEN sort_by = 'merchant_charge_desc' THEN `l4_customer_charge` END DESC,

					CASE WHEN sort_by = 'mercommision_asc' THEN `l1_merchant_earning` END ASC,
					CASE WHEN sort_by = 'mercommision_asc' THEN `l2_merchant_earning` END ASC,
					CASE WHEN sort_by = 'mercommision_asc' THEN `l3_merchant_earning` END ASC,
					CASE WHEN sort_by = 'mercommision_asc' THEN `l4_merchant_earning` END ASC,

					CASE WHEN sort_by = 'mercommision_desc' THEN `l1_merchant_earning` END DESC,
					CASE WHEN sort_by = 'mercommision_desc' THEN `l2_merchant_earning` END DESC,
					CASE WHEN sort_by = 'mercommision_desc' THEN `l3_merchant_earning` END DESC,
					CASE WHEN sort_by = 'mercommision_desc' THEN `l4_merchant_earning` END DESC,

					CASE WHEN sort_by = 'earning_asc' THEN `l1_earning` END ASC,
					CASE WHEN sort_by = 'earning_asc' THEN `l2_earning` END ASC,
					CASE WHEN sort_by = 'earning_asc' THEN `l3_earning` END ASC,
					CASE WHEN sort_by = 'earning_asc' THEN `l4_earning` END ASC,

					CASE WHEN sort_by = 'earning_desc' THEN `l1_earning` END DESC,
					CASE WHEN sort_by = 'earning_desc' THEN `l2_earning` END DESC,
					CASE WHEN sort_by = 'earning_desc' THEN `l3_earning` END DESC,
					CASE WHEN sort_by = 'earning_desc' THEN `l4_earning` END DESC,
					
					`l1_year`,`l1_month`, `l1_merchant_country_id`, `l1_merchant_id`, `l1_store_id`,
					`l2_year`,`l2_month`, `l2_merchant_country_id`, `l2_merchant_id`, `l2_store_id`,
					`l3_year`,`l3_month`, `l3_merchant_country_id`, `l3_merchant_id`, `l3_store_id`,
					`l4_year`,`l4_month`, `l4_merchant_country_id`, `l4_merchant_id`, `l4_store_id` ASC
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
