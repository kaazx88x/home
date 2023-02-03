<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEcardModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::unprepared("
            ALTER TABLE `generated_codes`
            CHANGE COLUMN `type` `type` SMALLINT(5) NOT NULL DEFAULT '0' COMMENT '2-Coupon,3-Ticket,4-E-Card' ,
            ADD COLUMN `product_id` INT(11) NOT NULL DEFAULT 0 AFTER `type`,
            ADD INDEX `product_idx` (`product_id` ASC);

            ALTER TABLE `generated_codes`
            CHANGE COLUMN `status` `status` SMALLINT(5) NOT NULL DEFAULT '1' COMMENT 'ticket/coupon\n1 => open\n2 => redeemed \n3 => canceled/refunded\n\ne-card\n0=>open | not purchased \n1 => purchased\n2 => redeemed\n3 => canceled' ;

            ALTER TABLE `nm_order`
            CHANGE COLUMN `order_type` `order_type` TINYINT(4) NOT NULL COMMENT '1-product,2-deals,3-coupon,4-ticket,5-e-card' ;

            ALTER TABLE `nm_product`
            CHANGE COLUMN `pro_type` `pro_type` SMALLINT(5) NOT NULL DEFAULT '1' COMMENT '1 => normal product\n2 => coupon\n3 => tickets\n4 => e-card' ;
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
