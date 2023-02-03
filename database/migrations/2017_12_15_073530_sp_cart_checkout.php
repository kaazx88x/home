<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SpCartCheckout extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
			DROP procedure IF EXISTS `cart_checkout`;

			CREATE PROCEDURE `cart_checkout`(IN cusid INT, IN country INT, IN trans_id VARCHAR(8), IN sname VARCHAR(225) CHARSET utf8, IN saddress1 VARCHAR(225) CHARSET utf8, IN saddress2 VARCHAR(225) CHARSET utf8, IN scity VARCHAR(225), IN scountry INT, IN spostal VARCHAR(10), IN stel VARCHAR(20), IN sstate INT, IN pay_method INT)
			BEGIN
				DECLARE tc_finished INT DEFAULT 0;
				DECLARE vcoin DECIMAL(11,4) DEFAULT 0;
				DECLARE tid INT;
				DECLARE cid INT;
				DECLARE pid INT;
				DECLARE qty INT;
				DECLARE rmks TEXT;
				DECLARE pricingid INT;
				DECLARE proprice DECIMAL(11,2);
				DECLARE proprice_ori DECIMAL(11,2);
				DECLARE provc DECIMAL(11,4);
				DECLARE protax DECIMAL(11,2);
				DECLARE order_id INT;
				DECLARE propurchase INT;
				DECLARE proname VARCHAR(255);
				DECLARE cus_vcoin DECIMAL(11,4);
				DECLARE commission DECIMAL(11,2);
				DECLARE charged DECIMAL(11,4);
				DECLARE p_charge DECIMAL(11,2);
				DECLARE pcharge_amt DECIMAL(11,4);
				DECLARE tot_pcharge_amt DECIMAL(11,4);
				DECLARE s_charge DECIMAL(11,2);
				DECLARE scharge_amt DECIMAL(11,4);
				DECLARE pro_price DECIMAL(11,2);
				DECLARE pro_dprice DECIMAL(11,2);
				DECLARE pro_dfrom DATETIME;
				DECLARE pro_dto DATETIME;
				DECLARE c_id INT;
				DECLARE currency_code VARCHAR(10);
				DECLARE currency_symbol VARCHAR(10);
				DECLARE currency_rate DECIMAL(11,2);
				DECLARE vcredit_convert DECIMAL(11,4);
				DECLARE tot_proprice DECIMAL(11,2);
				DECLARE today DATETIME;
				DECLARE order_value DECIMAL(11,2);
				DECLARE order_vcredit DECIMAL(11,4);
				DECLARE tot_proprice_vcredit DECIMAL(11,4);
				DECLARE json_attributes_name TEXT;
				DECLARE json_attributes_id TEXT;
				DECLARE price_quantity INT;
				DECLARE pro_quantity INT;
				DECLARE ship_fees_type INT;
				DECLARE ship_fees DECIMAL(11,2);
				DECLARE ship_fees_total DECIMAL(11,2);
				DECLARE ship_fees_credit DECIMAL(11,4);
				DECLARE wallet_credit DECIMAL(11,4);
				DECLARE product_type INT;
				DECLARE mer_id INT;
				DECLARE country_name VARCHAR(50);
				DECLARE state_name VARCHAR(50);
				DECLARE order_status INT DEFAULT 1;
				DECLARE order_type INT DEFAULT 1;
				DECLARE coupon_val VARCHAR(100);

				DECLARE curs CURSOR FOR SELECT tc.id, cus_id, product_id, tc.quantity, remarks ,pricing_id, nmt.mer_platform_charge, nmt.mer_service_charge, (SELECT co_name FROM nm_country WHERE co_id = scountry) as country, (SELECT name FROM nm_state WHERE id = sstate) as state FROM temp_cart tc
				LEFT JOIN nm_product np ON np.pro_id = tc.product_id
				LEFT JOIN nm_product_pricing npp ON npp.id = tc.pricing_id
				LEFT JOIN nm_merchant nmt ON nmt.mer_id = np.pro_mr_id
				LEFT JOIN nm_store nst ON nst.stor_id = np.pro_sh_id
				WHERE tc.cus_id = cusid AND npp.country_id = country AND npp.status = 1 AND nmt.mer_staus = 1 AND nst.stor_status = 1 AND np.pro_status = 1;
				DECLARE CONTINUE HANDLER FOR NOT FOUND SET tc_finished = 1;

				DROP TEMPORARY TABLE IF EXISTS tblResults;
				CREATE TEMPORARY TABLE IF NOT EXISTS tblResults  (
					product_type INT,
					product_name VARCHAR(255),
					quantity INT,
					order_vc DECIMAL(11,4),
					payment_method INT,
					order_amt DECIMAL(11,2),
					currency_symbol VARCHAR(10),
					json_attributes TEXT,
					serial_number TEXT,
					ship_name VARCHAR(100),
					ship_address1 VARCHAR(255),
					ship_address2 VARCHAR(255),
					ship_city VARCHAR(50),
					ship_country VARCHAR(50),
					ship_state VARCHAR(50),
					ship_postalcode VARCHAR(10),
					ship_phone VARCHAR(20)
				);

				OPEN curs;

				get_cart: LOOP
				FETCH curs into tid, cid, pid, qty, rmks, pricingid, p_charge, s_charge, country_name, state_name;
					IF tc_finished = 1 THEN LEAVE get_cart; END IF;

					#get customer details
					SELECT v_token INTO cus_vcoin FROM nm_customer WHERE cus_id = cid;
					SELECT credit INTO wallet_credit FROM customer_wallets WHERE customer_id = cid AND wallet_id = 1;

					#get commission details
					#SELECT platform_charge, service_charge INTO p_charge, s_charge FROM admin_setting WHERE id = 1;

					#get product pricing details
					SELECT price, discounted_price, discounted_from, discounted_to, country_id, quantity, attributes, attributes_name, shipping_fees_type, shipping_fees,
					CASE WHEN coupon_value IS NULL THEN '0' ELSE coupon_value END INTO pro_price, pro_dprice, pro_dfrom, pro_dto, c_id, price_quantity, json_attributes_id, json_attributes_name, ship_fees_type, ship_fees, coupon_val FROM nm_product_pricing WHERE id = pricingid AND country_id = country;

					#get country rate
					SELECT co_cursymbol, co_curcode, co_rate INTO currency_symbol, currency_code, currency_rate FROM nm_country WHERE co_id = c_id;

					SET today = NOW();

					IF ( (pro_dprice > 0) and (today  >= (CAST(pro_dfrom AS DATETIME)) and today <= (CAST(pro_dto AS DATETIME))) ) THEN
						#get product original price
						SET proprice_ori = pro_dprice;
					ELSE
						#get product original price
						SET proprice_ori = pro_price;
					END IF;

					#get product price
					SET proprice = proprice_ori;
					#get total product price
					SET tot_proprice = proprice * qty;
					#get order value with platform charge
					SET order_value = tot_proprice;

					SET vcredit_convert = 0.00;
					SET pcharge_amt = 0.00;
					SET scharge_amt = 0.00;
					SET order_vcredit = 0.00;

					IF (pay_method = 1) THEN
						#get shipping fees
						IF (ship_fees_type = 1) THEN
							SET ship_fees_total = ROUND(( ship_fees * qty ), 2);
						ELSEIF (ship_fees_type = 2) THEN
							SET ship_fees_total = ROUND(ship_fees, 2);
						ELSE
							SET ship_fees_total = 0.00;
						END IF;

						SET ship_fees_credit = ROUND(( ship_fees_total / currency_rate ), 4);

						#convert order value into vcredit
						SET vcredit_convert = ROUND(( order_value / currency_rate ), 4);

						#get platform charge
						SET pcharge_amt = ROUND(( vcredit_convert * (p_charge / 100) ), 4);

						#get service charge
						SET scharge_amt = ROUND(( (vcredit_convert + pcharge_amt) * (s_charge / 100)), 4);

						#get order vcredit value
						SET order_vcredit = ROUND(( vcredit_convert + pcharge_amt + scharge_amt + ship_fees_credit ), 4);
					END IF;

					#get product details
					SELECT pro_vtoken_value, pro_inctax, pro_no_of_purchase, pro_title_en, pro_qty, pro_type, pro_mr_id INTO provc, protax, propurchase, proname, pro_quantity, product_type, mer_id FROM nm_product nmp WHERE nmp.pro_id = pid;

					#get commission and its value
					SELECT mer_commission INTO commission FROM nm_product nmp LEFT JOIN nm_merchant nmm ON nmp.pro_mr_id = nmm.mer_id WHERE nmp.pro_id = pid;
					#convert total product price to vcredit
					SET tot_proprice_vcredit = ROUND(((proprice_ori * qty) / currency_rate), 4);
					SET charged = ROUND((commission * (tot_proprice_vcredit / 100)), 4);

					SET order_status = 1;
					SET order_type = 1;
					IF(product_type = 2) THEN
						SET order_status = 2;
						SET order_type = 3;
					ELSEIF(product_type = 3) THEN
						SET order_status = 2;
						SET order_type = 4;
					ELSEIF(product_type = 4) THEN
						SET order_status = 2;
						SET order_type = 5;
					END IF;

					#save order detail
					INSERT INTO nm_order
					(order_cus_id, order_pro_id, order_type, transaction_id, payer_name, order_qty, order_amt, order_tax, order_date, order_status, order_pro_color, order_pro_size, order_shipping_add, order_vtokens, remarks, created_at, updated_at, merchant_charge_percentage, merchant_charge_vtoken, payment_method, currency, currency_rate, product_original_price, product_price, total_product_price, cus_platform_charge_rate, cus_platform_charge_value, order_value, cus_service_charge_rate, cus_service_charge_value, order_vcredit, order_attributes, order_attributes_id, order_pricing_id, product_shipping_fees_type, product_shipping_fees, total_product_shipping_fees, total_product_shipping_fees_credit)
					VALUES
					(cid, pid, order_type, trans_id, sname, qty, 0, protax, NOW(), order_status, 0, 0, '', order_vcredit, rmks, NOW(), NOW(), commission, charged, pay_method, currency_code, currency_rate, proprice_ori, proprice, tot_proprice, p_charge, pcharge_amt, order_value,s_charge, scharge_amt, order_vcredit, json_attributes_name, json_attributes_id, pricingid, ship_fees_type, ship_fees, ship_fees_total, ship_fees_credit);
					SET order_id = LAST_INSERT_ID();

					#insert into generated codes table for product coupon/ticket type

					SET @generated_serial = null;
					IF(product_type = 2 or product_type = 3) THEN

						SET @count = qty;
						SET @generated_serial = '';

						WHILE (@count > 0) DO
							SET @found_code = 0;
							SET @allowedChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

							WHILE (@found_code = 0) DO
								SET @serial_number = '';
								SET @i = 0;

								WHILE (@i < 16) DO
									SET @serial_number = CONCAT(@serial_number, substring(@allowedChars, FLOOR(RAND() * LENGTH(@allowedChars) + 1), 1));
									SET @i = @i + 1;
								END WHILE;

								IF(product_type = 3) THEN
									SET @serial_number = CONCAT(pid, '_', @serial_number);
								END IF;

								IF((SELECT count(*) FROM generated_codes WHERE serial_number = @serial_number) = 0) THEN
									SET @found_code = 1;
								END IF;

							END WHILE;

							INSERT INTO generated_codes (type, order_id, customer_id, merchant_id, serial_number, value, status) VALUES (product_type, order_id, cid, mer_id, @serial_number, coupon_val, 1);
							SET @count := @count - 1;

							SET @generated_serial = CONCAT(@generated_serial, ',', @serial_number);

						END WHILE;

					ELSEIF(product_type = 4) THEN

						SET @count = qty;
						SET @generated_serial = '';

						WHILE (@count > 0) DO
							SET @serial_number = '';

							SELECT id, serial_number INTO @code_id, @serial_number FROM generated_codes WHERE status = 0 and merchant_id = mer_id and product_id = pid limit 1;

							SET @generated_serial = CONCAT(@generated_serial, ',', @serial_number);

							UPDATE generated_codes SET order_id = order_id, customer_id = cid, status = 1 WHERE id = @code_id;

							SET @count := @count - 1;
						END WHILE;

					END IF;

					#save shipping detail
					INSERT INTO nm_shipping
					(ship_name, ship_address1, ship_address2, ship_city_name, ship_country, ship_state_id, ship_postalcode, ship_phone, ship_order_id, ship_cus_id, created_at, updated_at)
					VALUES
					(sname, saddress1, saddress2, scity, scountry, sstate, spostal, stel, order_id, cid, NOW(), NOW());

					#update product : increase no of purchase
					SET propurchase := propurchase + qty;
					SET pro_quantity := pro_quantity - qty;
					UPDATE nm_product SET pro_no_of_purchase = propurchase, pro_qty = pro_quantity WHERE pro_id = pid;

					#update pricing quantity
					SET price_quantity := GREATEST(0, price_quantity- qty);

					IF json_attributes_id IS NULL THEN
						UPDATE nm_product_pricing SET quantity = price_quantity WHERE pro_id = pid;
					ELSE
						UPDATE nm_product_pricing SET quantity = price_quantity WHERE attributes = json_attributes_id AND pro_id = pid;
					END IF;

					INSERT INTO nm_product_quantity_log (pro_id, attributes, debit, credit, current_quantity, remarks) VALUES (pid, json_attributes_name, qty, 0, price_quantity, concat('Customer Purchase, Transaction ID : ', trans_id));

					#update customer : deduct vcoin if payment method is vcredit<< updated 29/11/2016
					IF (pay_method = 1) THEN
						SET cus_vcoin := cus_vcoin - order_vcredit;
						SET wallet_credit := wallet_credit - order_vcredit;

						#update customer v_token
						UPDATE nm_customer SET v_token = cus_vcoin WHERE cus_id = cid;

						#update customer wallet
						UPDATE customer_wallets SET credit = wallet_credit WHERE customer_id = cid AND wallet_id = 1;

						#add log
						INSERT INTO v_token_log
						(cus_id, credit_amount, debit_amount, order_id, remark, created_at, updated_at, wallet_id)
						VALUES
						(cid, 0, order_vcredit, order_id, 'Mall Purchased', NOW(), NOW(), 1);
					END IF;

					#clear temp_cart
					DELETE FROM temp_cart WHERE id = tid;

					INSERT INTO tblResults VALUES (product_type, proname, qty, CAST(order_vcredit AS DECIMAL(10,4)), pay_method, order_value, currency_symbol, json_attributes_name, @generated_serial, sname, saddress1, saddress2, scity, country_name, state_name, spostal, stel);

				END LOOP get_cart;
				CLOSE curs;

				SELECT * FROM tblResults;
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
