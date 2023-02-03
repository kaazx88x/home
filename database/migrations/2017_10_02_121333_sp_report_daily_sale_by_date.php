<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpReportDailySaleByDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared('
			CREATE PROCEDURE `sp_report_daily_sale_by_date`()
			BEGIN

			DECLARE today_date DATE;

			/* Today Date */
			SET today_date = date(DATE_SUB(DATE_ADD(now(), INTERVAL 8 HOUR), INTERVAL 1 DAY));

			DELETE FROM report_daily_sale_by_date WHERE transaction_date = today_date;


			/* ONLINE */
			/* STATUS 1 = SUCCESS */
			INSERT report_daily_sale_by_date (transaction_date, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT today_date, 1, nm_merchant.mer_id, 1, nm_store.stor_id, count(1), sum(nm_order.order_qty), sum(order_vtokens),  sum(cus_platform_charge_value),  sum(cus_service_charge_value), sum(merchant_charge_vtoken), NOW(), NOW()
			FROM nm_order
			INNER JOIN nm_product ON(nm_order.order_pro_id = nm_product.pro_id)
			INNER JOIN nm_store ON(nm_product.pro_sh_id = nm_store.stor_id)
			INNER JOIN nm_merchant ON(nm_store.stor_merchant_id = nm_merchant.mer_id)
			WHERE DATE(DATE_ADD(nm_order.order_date, INTERVAL 8 HOUR)) = today_date 
			AND nm_order.order_status IN(1,2,3,4)
			GROUP BY DATE(DATE_ADD(nm_order.order_date, INTERVAL 8 HOUR)), nm_merchant.mer_id, nm_store.stor_id;

			/* STATUS 2 = CANCEL */
			INSERT report_daily_sale_by_date (transaction_date, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT today_date, 2, nm_merchant.mer_id, 1, nm_store.stor_id, count(1), sum(nm_order.order_qty), sum(order_vtokens),  sum(cus_platform_charge_value),  sum(cus_service_charge_value), sum(merchant_charge_vtoken), NOW(), NOW()
			FROM nm_order
			INNER JOIN nm_product ON(nm_order.order_pro_id = nm_product.pro_id)
			INNER JOIN nm_store ON(nm_product.pro_sh_id = nm_store.stor_id)
			INNER JOIN nm_merchant ON(nm_store.stor_merchant_id = nm_merchant.mer_id)
			WHERE DATE(DATE_ADD(nm_order.updated_at, INTERVAL 8 HOUR)) = today_date 
			AND nm_order.order_status IN(5)
			GROUP BY DATE(DATE_ADD(nm_order.updated_at, INTERVAL 8 HOUR)), nm_merchant.mer_id, nm_store.stor_id;

			/* STATUS 3 = REFUNDED */
			INSERT report_daily_sale_by_date (transaction_date, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT today_date, 3, nm_merchant.mer_id, 1, nm_store.stor_id, count(1), sum(nm_order.order_qty), sum(order_vtokens),  sum(cus_platform_charge_value),  sum(cus_service_charge_value), sum(merchant_charge_vtoken), NOW(), NOW()
			FROM nm_order
			INNER JOIN nm_product ON(nm_order.order_pro_id = nm_product.pro_id)
			INNER JOIN nm_store ON(nm_product.pro_sh_id = nm_store.stor_id)
			INNER JOIN nm_merchant ON(nm_store.stor_merchant_id = nm_merchant.mer_id)
			WHERE DATE(DATE_ADD(nm_order.updated_at, INTERVAL 8 HOUR)) = today_date 
			AND nm_order.order_status IN(6)
			GROUP BY DATE(DATE_ADD(nm_order.updated_at, INTERVAL 8 HOUR)), nm_merchant.mer_id, nm_store.stor_id;


			/* OFFLINE */
			/* STATUS 1 = SUCCESS */
			INSERT report_daily_sale_by_date (transaction_date, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT today_date, 1, nm_merchant.mer_id, 2, nm_store.stor_id, count(1), count(1), sum(order_total_token),  sum(merchant_platform_charge_token),  sum(customer_charge_token), sum(merchant_charge_token), NOW(), NOW()
			FROM order_offline
			INNER JOIN nm_store ON(order_offline.store_id = nm_store.stor_id)
			INNER JOIN nm_merchant ON(order_offline.mer_id = nm_merchant.mer_id)
			WHERE DATE(DATE_ADD(order_offline.paid_date, INTERVAL 8 HOUR)) = today_date 
			AND order_offline.status IN(1)
			GROUP BY DATE(DATE_ADD(order_offline.paid_date, INTERVAL 8 HOUR)), nm_merchant.mer_id, nm_store.stor_id;

			/* STATUS 2 = CANCEL */
			INSERT report_daily_sale_by_date (transaction_date, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT today_date, 2, nm_merchant.mer_id, 2, nm_store.stor_id, count(1), count(1), sum(order_total_token),  sum(merchant_platform_charge_token),  sum(customer_charge_token), sum(merchant_charge_token), NOW(), NOW()
			FROM order_offline
			INNER JOIN nm_store ON(order_offline.store_id = nm_store.stor_id)
			INNER JOIN nm_merchant ON(order_offline.mer_id = nm_merchant.mer_id)
			WHERE DATE(DATE_ADD(order_offline.updated_at, INTERVAL 8 HOUR)) = today_date 
			AND order_offline.status IN(2,3)
			GROUP BY DATE(DATE_ADD(order_offline.updated_at, INTERVAL 8 HOUR)), nm_merchant.mer_id, nm_store.stor_id;

			/* STATUS 3 = REFUNDED */
			INSERT report_daily_sale_by_date (transaction_date, order_status,  merchant_id, online_offline_status, store_id, total_order, total_product, sales_amount, platform_charge, customer_charge, merchant_commission, created_at, updated_at)
			SELECT today_date, 3, nm_merchant.mer_id, 2, nm_store.stor_id, count(1), count(1), sum(order_total_token),  sum(merchant_platform_charge_token),  sum(customer_charge_token), sum(merchant_charge_token), NOW(), NOW()
			FROM order_offline
			INNER JOIN nm_store ON(order_offline.store_id = nm_store.stor_id)
			INNER JOIN nm_merchant ON(order_offline.mer_id = nm_merchant.mer_id)
			WHERE DATE(DATE_ADD(order_offline.updated_at, INTERVAL 8 HOUR)) = today_date 
			AND order_offline.status IN(4)
			GROUP BY DATE(DATE_ADD(order_offline.updated_at, INTERVAL 8 HOUR)), nm_merchant.mer_id, nm_store.stor_id;

			SELECT today_date;

			END
		
		');
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
