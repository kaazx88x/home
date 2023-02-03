<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpDailyCreditSummary extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
			CREATE PROCEDURE `sp_report_daily_credit`()
			BEGIN

				DECLARE today_date DATE;

				/* Today Date */
				SET today_date = date(DATE_SUB(DATE_ADD(now(), INTERVAL 8 HOUR), INTERVAL 1 DAY));

				DELETE FROM report_daily_credit WHERE report_daily_credit.date = today_date;

				/* USER TYPE - USER */
				/* Credit Type - 1 */
				INSERT report_daily_credit(date, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT today_date, 'user', 1, max(nm_country.co_id), sum(credit_amount), sum(debit_amount), NOW(), NOW()
				FROM v_token_log
				INNER JOIN nm_customer ON(v_token_log.cus_id = nm_customer.cus_id)
				LEFT JOIN nm_country ON(nm_country.co_id = nm_customer.cus_country)
				WHERE v_token_log.order_id IS NULL
				AND v_token_log.offline_order_id IS NULL
				AND v_token_log.from IS NOT NULL
				AND v_token_log.svi_wallet IS NOT NULL 
				AND DATE(DATE_ADD(v_token_log.created_at, INTERVAL 8 HOUR)) = today_date 
				GROUP BY DATE(DATE_ADD(v_token_log.created_at, INTERVAL 8 HOUR)), nm_country.co_id;

				/* Credit Type - 2 */
				INSERT report_daily_credit(date, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT today_date, 'user', 2, max(nm_country.co_id), sum(credit_amount), sum(debit_amount), NOW(), NOW()
				FROM v_token_log
				INNER JOIN nm_customer ON(v_token_log.cus_id = nm_customer.cus_id)
				LEFT JOIN nm_country ON(nm_country.co_id = nm_customer.cus_country)
				WHERE v_token_log.order_id IS NOT NULL
				AND v_token_log.order_id > 0
				AND v_token_log.offline_order_id IS NULL
				AND DATE(DATE_ADD(v_token_log.created_at, INTERVAL 8 HOUR)) = today_date 
				GROUP BY DATE(DATE_ADD(v_token_log.created_at, INTERVAL 8 HOUR)), nm_country.co_id;
				
				/* Credit Type - 3 */
				INSERT report_daily_credit(date, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT today_date, 'user', 3, max(nm_country.co_id), sum(credit_amount), sum(debit_amount), NOW(), NOW()
				FROM v_token_log
				INNER JOIN nm_customer ON(v_token_log.cus_id = nm_customer.cus_id)
				LEFT JOIN nm_country ON(nm_country.co_id = nm_customer.cus_country)
				WHERE v_token_log.offline_order_id IS NOT NULL
				AND v_token_log.offline_order_id > 0
				AND v_token_log.order_id is NULL
				AND DATE(DATE_ADD(v_token_log.created_at, INTERVAL 8 HOUR)) = today_date 
				GROUP BY DATE(DATE_ADD(v_token_log.created_at, INTERVAL 8 HOUR)), nm_country.co_id;

				 /* Credit Type - 4 */
				INSERT report_daily_credit(date, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT today_date, 'user', 4, max(nm_country.co_id), sum(credit_amount), sum(debit_amount), NOW(), NOW()
				FROM v_token_log
				INNER JOIN nm_customer ON(v_token_log.cus_id = nm_customer.cus_id)
				LEFT JOIN nm_country ON(nm_country.co_id = nm_customer.cus_country)
				WHERE (v_token_log.offline_order_id is NULL  OR v_token_log.offline_order_id = 0)
				AND v_token_log.from is NULL
				AND v_token_log.svi_wallet is NULL
				AND (v_token_log.order_id = 0  OR v_token_log.order_id IS NULL)
				AND DATE(DATE_ADD(v_token_log.created_at, INTERVAL 8 HOUR)) = today_date 
				GROUP BY DATE(DATE_ADD(v_token_log.created_at, INTERVAL 8 HOUR)), nm_country.co_id;
			   

				/* USER TYPE - MERCHANT */
				/* Credit Type - 1 */
				INSERT report_daily_credit(date, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT today_date, 'merchant', 1, max(nm_country.co_id), sum(credit_amount), sum(debit_amount), NOW(), NOW()
				FROM merchant_vtoken_log
				INNER JOIN nm_merchant on(nm_merchant.mer_id = merchant_vtoken_log.mer_id)
				LEFT JOIN nm_country on(nm_country.co_id = nm_merchant.mer_co_id)
				WHERE merchant_vtoken_log.offline_order_id is NULL
				AND merchant_vtoken_log.order_id is NULL
				AND merchant_vtoken_log.withdraw_id is NOT NULL
				AND DATE(DATE_ADD(merchant_vtoken_log.created_at, INTERVAL 8 HOUR)) = today_date 
				GROUP BY DATE(DATE_ADD(merchant_vtoken_log.created_at, INTERVAL 8 HOUR)), nm_country.co_id ;

				/* Credit Type - 2 */
				INSERT report_daily_credit(date, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT today_date, 'merchant', 2, max(nm_country.co_id), sum(credit_amount), sum(debit_amount), NOW(), NOW()
				FROM merchant_vtoken_log
				INNER JOIN nm_merchant on(nm_merchant.mer_id = merchant_vtoken_log.mer_id)
				LEFT JOIN nm_country on(nm_country.co_id = nm_merchant.mer_co_id)
				WHERE merchant_vtoken_log.order_id is NOT NULL
				AND merchant_vtoken_log.order_id > 0
				AND merchant_vtoken_log.offline_order_id is NULL
				AND merchant_vtoken_log.withdraw_id is NULL
				AND DATE(DATE_ADD(merchant_vtoken_log.created_at, INTERVAL 8 HOUR)) = today_date 
				GROUP BY DATE(DATE_ADD(merchant_vtoken_log.created_at, INTERVAL 8 HOUR)), nm_country.co_id ;
				
				/* Credit Type - 3 */
				INSERT report_daily_credit(date, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT today_date, 'merchant', 3, max(nm_country.co_id), sum(credit_amount), sum(debit_amount), NOW(), NOW()
				FROM merchant_vtoken_log
				INNER JOIN nm_merchant on(nm_merchant.mer_id = merchant_vtoken_log.mer_id)
				LEFT JOIN nm_country on(nm_country.co_id = nm_merchant.mer_co_id)
				WHERE merchant_vtoken_log.offline_order_id is NOT NULL
				AND merchant_vtoken_log.offline_order_id > 0
				AND merchant_vtoken_log.order_id is NULL
				AND merchant_vtoken_log.withdraw_id is NULL
				AND DATE(DATE_ADD(merchant_vtoken_log.created_at, INTERVAL 8 HOUR)) = today_date 
				GROUP BY DATE(DATE_ADD(merchant_vtoken_log.created_at, INTERVAL 8 HOUR)), nm_country.co_id ;
				
				/* Credit Type - 4 */
				INSERT report_daily_credit(date, user_type, credit_type, country_id, credit_amount, debit_amount, created_at, updated_at)
				SELECT today_date, 'merchant', 4, max(nm_country.co_id), sum(credit_amount), sum(debit_amount), NOW(), NOW()
				FROM merchant_vtoken_log
				INNER JOIN nm_merchant on(nm_merchant.mer_id = merchant_vtoken_log.mer_id)
				LEFT JOIN nm_country on(nm_country.co_id = nm_merchant.mer_co_id)
				WHERE merchant_vtoken_log.offline_order_id is NULL
				AND merchant_vtoken_log.withdraw_id is NULL
				AND (merchant_vtoken_log.order_id = 0 OR merchant_vtoken_log.order_id is NULL)
				AND DATE(DATE_ADD(merchant_vtoken_log.created_at, INTERVAL 8 HOUR)) = today_date 
				GROUP BY DATE(DATE_ADD(merchant_vtoken_log.created_at, INTERVAL 8 HOUR)), nm_country.co_id ;
				
				SELECT today_date;

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
