<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InsertPermissionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('permission')->insert([
            'id' => 1,
            'permission_group' => 'Administrator Role',
            'display_sorting' => 1.000,
            'permission_name' => 'adminmanagelist',
            'display_name' => 'View Admin User Role',
            'description' => 'Allow admin user to view admin role list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 2,
            'permission_group' => 'Administrator Role',
            'display_sorting' => 1.100,
            'permission_name' => 'adminmanagecreate',
            'display_name' => 'Create New User Role',
            'description' => 'Allow admin user to create new admin role',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 3,
            'permission_group' => 'Administrator Role',
            'display_sorting' => 1.200,
            'permission_name' => 'adminmanageedit',
            'display_name' => 'Edit User Role',
            'description' => 'Allow admin user to edit existing admin role',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 6,
            'permission_group' => 'Courier - Settings',
            'display_sorting' => 6.000,
            'permission_name' => 'settingcourierlist',
            'display_name' => 'View Courier',
            'description' => 'Allow admin user to view courier list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 7,
            'permission_group' => 'Courier - Settings',
            'display_sorting' => 7.000,
            'permission_name' => 'settingcouriercreate',
            'display_name' => 'Create New Courier',
            'description' => 'Allow admin user to create new courier ',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 8,
            'permission_group' => 'Courier - Settings',
            'display_sorting' => 8.000,
            'permission_name' => 'settingcourieredit',
            'display_name' => 'Edit Courier',
            'description' => 'Allow admin user to edit existing courier',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 9,
            'permission_group' => 'Courier - Settings',
            'display_sorting' => 9.000,
            'permission_name' => 'settingcourierdelete',
            'display_name' => 'Delete Courier',
            'description' => 'Allow admin user to delete courier',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 10,
            'permission_group' => 'Countries - Settings',
            'display_sorting' => 10.000,
            'permission_name' => 'settingcountrieslist',
            'display_name' => 'View Countries',
            'description' => 'Allow admin user to view countries list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 11,
            'permission_group' => 'Countries - Settings',
            'display_sorting' => 11.000,
            'permission_name' => 'settingcountriescreate',
            'display_name' => 'Create New Countries',
            'description' => 'Allow admin user to create new countries',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 12,
            'permission_group' => 'Countries - Settings',
            'display_sorting' => 12.000,
            'permission_name' => 'settingcountriesedit',
            'display_name' => 'Edit Countries',
            'description' => 'Allow admin user to edit existing countries',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 13,
            'permission_group' => 'Countries - Settings',
            'display_sorting' => 13.000,
            'permission_name' => 'settingcountriesdelete',
            'display_name' => 'Delete Countries',
            'description' => 'Allow admin user to delete countries',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 14,
            'permission_group' => 'Categories Online - Settings',
            'display_sorting' => 14.000,
            'permission_name' => 'settingcategoriesonlinelist',
            'display_name' => 'View Categories Online',
            'description' => 'Allow admin user to view categories online list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 15,
            'permission_group' => 'Categories Online - Settings',
            'display_sorting' => 15.000,
            'permission_name' => 'settingcategoriesonlinecreate',
            'display_name' => 'Create Categories Online',
            'description' => 'Allow admin user to create new categories online',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 16,
            'permission_group' => 'Categories Online - Settings',
            'display_sorting' => 16.000,
            'permission_name' => 'settingcategoriesonlineedit',
            'display_name' => 'Edit Categories Online',
            'description' => 'Allow admin user to edit categories online',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 17,
            'permission_group' => 'Categories Online - Settings',
            'display_sorting' => 17.000,
            'permission_name' => 'settingcategoriesonlinedelete',
            'display_name' => 'Delete Categories Online',
            'description' => 'Allow admin user to delete categories online',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 18,
            'permission_group' => 'Categories Offline - Settings',
            'display_sorting' => 18.000,
            'permission_name' => 'settingcategoriesofflinelist',
            'display_name' => 'View Categories Offline',
            'description' => 'Allow admin user to view categories offline list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 19,
            'permission_group' => 'Categories Offline - Settings',
            'display_sorting' => 19.000,
            'permission_name' => 'settingcategoriesofflinecreate',
            'display_name' => 'Create Categories Offline',
            'description' => 'Allow admin user to create new categories offline',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 20,
            'permission_group' => 'Categories Offline - Settings',
            'display_sorting' => 20.000,
            'permission_name' => 'settingcategoriesofflineedit',
            'display_name' => 'Edit Categories Offline',
            'description' => 'Allow admin user to edit categories offline',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 21,
            'permission_group' => 'Categories Offline - Settings',
            'display_sorting' => 21.000,
            'permission_name' => 'settingcategoriesofflinedelete',
            'display_name' => 'Delete Categories Offline',
            'description' => 'Allow admin user to delete categories offline',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 22,
            'permission_group' => 'CMS - Settings',
            'display_sorting' => 22.000,
            'permission_name' => 'settingcmslist',
            'display_name' => 'View CMS',
            'description' => 'Allow admin user to view cms list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 23,
            'permission_group' => 'CMS - Settings',
            'display_sorting' => 23.000,
            'permission_name' => 'settingcmscreate',
            'display_name' => 'Create New CMS',
            'description' => 'Allow admin user to create new cms',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 24,
            'permission_group' => 'CMS - Settings',
            'display_sorting' => 24.000,
            'permission_name' => 'settingcmsedit',
            'display_name' => 'Edit CMS',
            'description' => 'Allow admin user to edit existing cms',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 25,
            'permission_group' => 'CMS - Settings',
            'display_sorting' => 25.000,
            'permission_name' => 'settingcmsdelete',
            'display_name' => 'Delete CMS',
            'description' => 'Allow admin user to delete cms',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 26,
            'permission_group' => 'Banner - Settings',
            'display_sorting' => 26.000,
            'permission_name' => 'settingbannerlist',
            'display_name' => 'View Banner List',
            'description' => 'Allow admin user to view banner list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 27,
            'permission_group' => 'Banner - Settings',
            'display_sorting' => 27.000,
            'permission_name' => 'settingbannercreate',
            'display_name' => 'Create New Banner',
            'description' => 'Allow admin user to create new banner',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 28,
            'permission_group' => 'Banner - Settings',
            'display_sorting' => 28.000,
            'permission_name' => 'settingbanneredit',
            'display_name' => 'Edit Banner',
            'description' => 'Allow admin user to edit existing banner',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 29,
            'permission_group' => 'Banner - Settings',
            'display_sorting' => 29.000,
            'permission_name' => 'settingbannerdelete',
            'display_name' => 'Delete Banner',
            'description' => 'Allow admin user to delete banner',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 30,
            'permission_group' => 'Filter - Settings',
            'display_sorting' => 30.000,
            'permission_name' => 'settingfilterlist',
            'display_name' => 'View Filter',
            'description' => 'Allow admin user to view filter list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 31,
            'permission_group' => 'Filter - Settings',
            'display_sorting' => 31.000,
            'permission_name' => 'settingfiltercreate',
            'display_name' => 'Create Filter',
            'description' => 'Allow admin user to create new filter',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 32,
            'permission_group' => 'Filter - Settings',
            'display_sorting' => 32.000,
            'permission_name' => 'settingfilteredit',
            'display_name' => 'Edit Filter',
            'description' => 'Allow admin user to edit existing filter',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 33,
            'permission_group' => 'Commission - Settings',
            'display_sorting' => 33.000,
            'permission_name' => 'settingcommissionlist',
            'display_name' => 'View Commission',
            'description' => 'Allow admin user to view commission list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 34,
            'permission_group' => 'Commission - Settings',
            'display_sorting' => 34.000,
            'permission_name' => 'settingcommissionedit',
            'display_name' => 'Edit Commission',
            'description' => 'Allow admin user to edit commission',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 35,
            'permission_group' => 'Products',
            'display_sorting' => 35.000,
            'permission_name' => 'productmanagelist',
            'display_name' => 'View Product',
            'description' => 'Allow admin user to view product list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 36,
            'permission_group' => 'Products',
            'display_sorting' => 36.000,
            'permission_name' => 'productmanagecreate',
            'display_name' => 'Create Product',
            'description' => 'Allow admin user to view product list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' =>37 ,
            'permission_group' => 'Products',
            'display_sorting' => 37.000,
            'permission_name' => 'productmanageedit',
            'display_name' => 'Edit Product',
            'description' => 'Allow admin user to edit existing product',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 38,
            'permission_group' => 'Products',
            'display_sorting' => 38.000,
            'permission_name' => 'productmanagesetstatus',
            'display_name' => 'Set Product Status',
            'description' => 'Allow admin user to set product status to active or inactive',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 39,
            'permission_group' => 'Products',
            'display_sorting' => 39.000,
            'permission_name' => 'productshippinganddeliverylist',
            'display_name' => 'View Product Shipping & Delivery List',
            'description' => 'Allow admin user to view product shipping & delivery list and detail. ( Edit action permission in sold out page is control by Edit Product Permission Setting)',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 40,
            'permission_group' => 'Customers',
            'display_sorting' => 40.000,
            'permission_name' => 'customermanagelist',
            'display_name' => 'View Customer',
            'description' => 'Allow admin user to view customer list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 41,
            'permission_group' => 'Customers',
            'display_sorting' => 41.000,
            'permission_name' => 'customermanagecreate',
            'display_name' => 'Create Customer',
            'description' => 'Allow admin user to create new customer',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 42,
            'permission_group' => 'Customers',
            'display_sorting' => 42.000,
            'permission_name' => 'customermanageedit',
            'display_name' => 'Edit Customer',
            'description' => 'Allow admin user to edit existing customer',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 43,
            'permission_group' => 'Customers',
            'display_sorting' => 43.000,
            'permission_name' => 'customermanagesetstatus',
            'display_name' => 'Set Customer Status',
            'description' => 'Allow admin user to set customer status. Can active the customer, block the customer, lock the customer & unlock the customer',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 44,
            'permission_group' => 'Customers',
            'display_sorting' => 44.000,
            'permission_name' => 'customermanageseteditemail',
            'display_name' => 'Edit Customer Email',
            'description' => 'Allow admin user to edit customer email',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 45,
            'permission_group' => 'Customers',
            'display_sorting' => 45.000,
            'permission_name' => 'customermanageseteditcustomerphone',
            'display_name' => 'Edit Customer Phone',
            'description' => 'Allow admin user to edit customer phone',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' =>46 ,
            'permission_group' => 'Customers',
            'display_sorting' => 46.000,
            'permission_name' => 'customermanagemicreditreport',
            'display_name' => 'View Mi Credit Report',
            'description' => 'Allow admin user to view Mi Credit report',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 47,
            'permission_group' => 'Customers',
            'display_sorting' => 47.000,
            'permission_name' => 'customermanagemicreditreportexport',
            'display_name' => 'Mi Credit Report Export',
            'description' => 'Allow admin user to export Mi Credit report',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 48,
            'permission_group' => 'Customers',
            'display_sorting' => 48.000,
            'permission_name' => 'customermanagemmicreditedit',
            'display_name' => 'Manage Mi Credit',
            'description' => 'Allow admin user to manage Mi Credit',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 49,
            'permission_group' => 'Customers',
            'display_sorting' => 49.000,
            'permission_name' => 'customermanagehardresetpassword',
            'display_name' => 'Hard Reset Password',
            'description' => 'Allow admin user to hard reset password (Hard reset password is allowed, mean also allowed soft reset password)',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 50,
            'permission_group' => 'Customers',
            'display_sorting' =>50.000 ,
            'permission_name' => 'customermanagesoftresetpassword',
            'display_name' => 'Soft Reset Password',
            'description' => 'Allow admin user to soft reset password',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 51,
            'permission_group' => 'Customers',
            'display_sorting' => 51.000,
            'permission_name' => 'customermanagehardresetsecurecode',
            'display_name' => 'Hard Reset Secure Code',
            'description' => 'Allow admin user to hard reset secure code (Hard reset secure code is allowed, mean also allowed soft reset secure code)',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 52,
            'permission_group' => 'Customers',
            'display_sorting' => 52.000,
            'permission_name' => 'customermanagesoftresetsecurecode',
            'display_name' => 'Soft Reset Secure Code',
            'description' => 'Allow admin user to soft reset secure code',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 53,
            'permission_group' => 'Customers',
            'display_sorting' => 53.000,
            'permission_name' => 'customerinquirieslist',
            'display_name' => 'View Inquiries',
            'description' => 'Allow admin user to view inquiries list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 54,
            'permission_group' => 'Customers',
            'display_sorting' => 54.000,
            'permission_name' => 'customerinquiriesdelete',
            'display_name' => 'Delete Inquiries',
            'description' => 'Allow admin user to delete inquiries',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 55,
            'permission_group' => 'Transactions Online And Coupon Order',
            'display_sorting' => 55.000,
            'permission_name' => 'transactiononlineorderslist',
            'display_name' => 'View Transaction Online & Coupons Order',
            'description' => 'Allow admin user to view transaction online & coupon order list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 56,
            'permission_group' => 'Transactions Online And Coupon Order',
            'display_sorting' => 56.000,
            'permission_name' => 'transactiononlineorderslistexport',
            'display_name' => 'Export Transaction Online & Coupons Orders',
            'description' => 'Allow admin user to export transaction online & coupon order list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 57,
            'permission_group' => 'Transactions Online And Coupon Order',
            'display_sorting' => 57.000,
            'permission_name' => 'transactiononlineacceptorder',
            'display_name' => 'Accept Transaction Online Order',
            'description' => 'Allow admin user to accept transaction online order',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 58,
            'permission_group' => 'Transactions Online And Coupon Order',
            'display_sorting' => 58.000,
            'permission_name' => 'transactiononlineupdateshippinginfo',
            'display_name' => 'Update Transaction Online Shipping Info',
            'description' => 'Allow admin user to update transaction online shipping info',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 59,
            'permission_group' => 'Transactions Online And Coupon Order',
            'display_sorting' => 59.000,
            'permission_name' => 'transactiononlinecancelorder',
            'display_name' => 'Cancel Transaction Online & Coupons Order',
            'description' => 'Allow admin user to cancel the transaction online order & coupons order',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 60,
            'permission_group' => 'Transactions Online And Coupon Order',
            'display_sorting' => 60.000,
            'permission_name' => 'transactiononlineorderrefund',
            'display_name' => 'Refund Transaction Online & Coupons Order',
            'description' => 'Allow admin user to make transaction online order refund & coupons order refund',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 61,
            'permission_group' => 'Transactions Offline',
            'display_sorting' => 61.000,
            'permission_name' => 'transactionofflineorderslist',
            'display_name' => 'View Transaction Offline Orders',
            'description' => 'Allow admin user to view transaction offline order list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 63,
            'permission_group' => 'Transactions Offline',
            'display_sorting' => 61.200,
            'permission_name' => 'transactionofflineorderrefund',
            'display_name' => 'Refund Transaction Offline Order ',
            'description' => 'Allow admin user to make transaction offline order refurd',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 64,
            'permission_group' => 'Transactions - Fund Request',
            'display_sorting' => 64.000,
            'permission_name' => 'transactionfundrequestlist',
            'display_name' => 'View Transaction Fund Request',
            'description' => 'Allow admin user to view fund request list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 65,
            'permission_group' => 'Transactions - Fund Request',
            'display_sorting' => 65.000,
            'permission_name' => 'transactionfundrequestlistexport',
            'display_name' => 'Export Transaction Fund Request',
            'description' => 'Allow admin user to export fund request list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 66,
            'permission_group' => 'Transactions - Fund Request',
            'display_sorting' => 66.000,
            'permission_name' => 'transactionfundrequesfundpaid',
            'display_name' => 'Update Transaction Fund Request Status To Paid',
            'description' => 'Allow admin user to update fund request status to paid',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 67,
            'permission_group' => 'Transactions - Fund Request',
            'display_sorting' => 67.000,
            'permission_name' => 'transactionfundrequesapproval',
            'display_name' => 'Approval Transaction Fund Request ',
            'description' => 'Allow admin user to approve fund request',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 68,
            'permission_group' => 'Transactions Online And Coupon Order',
            'display_sorting' => 60.120,
            'permission_name' => 'transactionredeemcoupons',
            'display_name' => 'Redeem Coupons',
            'description' => 'Allow admin user to redeem coupons',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 69,
            'permission_group' => 'Transactions Online And Coupon Order',
            'display_sorting' => 60.200,
            'permission_name' => 'transactioncancelcoupons',
            'display_name' => 'Cancel Coupons',
            'description' => 'Allow admin user to cancel coupons (admin allow can cancel this coupons order)',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 70,
            'permission_group' => 'Merchants',
            'display_sorting' => 70.000,
            'permission_name' => 'merchantonlinelist',
            'display_name' => 'View Merchant',
            'description' => 'Allow admin user to view merchant online & offline list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 71,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.000,
            'permission_name' => 'merchantonlinecreate',
            'display_name' => 'Create Merchant',
            'description' => 'Allow admin user to create new merchant online & offline',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 72,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.100,
            'permission_name' => 'merchantonlineedit',
            'display_name' => 'Edit Merchant',
            'description' => 'Allow admin user to edit merchant online & offline',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 73,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.200,
            'permission_name' => 'merchantonlineeditmerchanttype',
            'display_name' => 'Edit Merchant Type',
            'description' => 'Allow admin user to edit online & offline merchant type',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 74,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.210,
            'permission_name' => 'merchantonlineeditmerchantusername',
            'display_name' => 'Edit Merchant Username',
            'description' => 'Allow admin user to edit online & offline merchant username',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 75,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.300,
            'permission_name' => 'merchantonlineeditmerchantemail',
            'display_name' => 'Edit Merchant Email',
            'description' => 'Allow admin user to edit online & offline merchant email',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 76,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.400,
            'permission_name' => 'merchantonlineeditmerchantphone',
            'display_name' => 'Edit Merchant Phone',
            'description' => 'Allow admin user to edit online & offline merchant phone',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 77,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.500,
            'permission_name' => 'merchantonlineeditmerchantbankinfo',
            'display_name' => 'Edit Merchant Bank Info',
            'description' => 'Allow admin user to edit online & offline merchant bank info',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 78,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.600,
            'permission_name' => 'merchantonlineblockmerchant',
            'display_name' => 'Update Merchant Status',
            'description' => 'Allow admin user to block, unblock & approve the online and offline merchant',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 79,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.610,
            'permission_name' => 'merchantonlinelockmerchant',
            'display_name' => 'Lock & Unlock Merchant',
            'description' => 'Allow admin user to lock & unlock the online and offline merchant',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 80,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.700,
            'permission_name' => 'merchantonlinehardresetpassword',
            'display_name' => 'Hard Reset Password',
            'description' => 'Allow admin user to hard reset password for online and offline merchant (Hard reset password is allowed, mean also allowed soft reset password)',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 81,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.710,
            'permission_name' => 'merchantonlinesoftresetpassword',
            'display_name' => 'Soft Reset Password',
            'description' => 'Allow admin user to soft reset password for online and offline merchant',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 82,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.800,
            'permission_name' => 'merchantonlinemicreditreport',
            'display_name' => 'View Mi Credit Report',
            'description' => 'Allow admin user to view online & offline merchant Mi Credit report',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 83,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.810,
            'permission_name' => 'merchantonlinemanagemicredit',
            'display_name' => 'Manage Mi Credit',
            'description' => 'Allow admin user to manage online & offline merchant Mi Credit',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 84,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.801,
            'permission_name' => 'merchantonlinemicreditreportexport',
            'display_name' => 'Export Mi Credit Report',
            'description' => 'Allow admin user to export online & offline merchant Mi Credit report',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 85,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.900,
            'permission_name' => 'merchantonlineresentactivationemail',
            'display_name' => 'Resend Activation Email',
            'description' => 'Allow admin user to resend activation email for offline merchant',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 86,
            'permission_group' => 'Stores',
            'display_sorting' => 80.000,
            'permission_name' => 'storelist',
            'display_name' => 'View Store',
            'description' => 'Allow admin user to view online & offline store list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 87,
            'permission_group' => 'Stores',
            'display_sorting' => 81.000,
            'permission_name' => 'storeedit',
            'display_name' => 'Edit Store',
            'description' => 'Allow admin user to edit existing online & offline store',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 88,
            'permission_group' => 'Stores',
            'display_sorting' => 82.000,
            'permission_name' => 'storesetstatus',
            'display_name' => 'Set Store Status',
            'description' => 'Allow admin user to block or active the store',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 89,
            'permission_group' => 'Stores',
            'display_sorting' => 83.000,
            'permission_name' => 'storetogglelisted',
            'display_name' => 'Store Toggle Listed (Offline)',
            'description' => 'Allow admin user to toggle listed (For offline)',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 90,
            'permission_group' => 'Stores',
            'display_sorting' => 84.000,
            'permission_name' => 'storeuserlist',
            'display_name' => 'View Store User',
            'description' => 'Allow admin user to view online & offline store user list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 91,
            'permission_group' => 'Stores',
            'display_sorting' => 85.000,
            'permission_name' => 'storeusercreate',
            'display_name' => 'Create Store User',
            'description' => 'Allow admin user to create online & offline store user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 92,
            'permission_group' => 'Stores',
            'display_sorting' => 86.000,
            'permission_name' => 'storeuseredit',
            'display_name' => 'Edit Store User',
            'description' => 'Allow admin user to edit existing online & offline store user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 93,
            'permission_group' => 'Stores',
            'display_sorting' => 87.000,
            'permission_name' => 'storeuserresetpassword',
            'display_name' => 'Reset Store User Password',
            'description' => 'Allow admin user to reset password for online & offline store user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 94,
            'permission_group' => 'Stores',
            'display_sorting' => 88.000,
            'permission_name' => 'storeuserlockuser',
            'display_name' => 'Lock & Unlock Store User',
            'description' => 'Allow admin user to lock or unlock for online & offline store user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 95,
            'permission_group' => 'Stores',
            'display_sorting' => 89.000,
            'permission_name' => 'storeusersetuserstatus',
            'display_name' => 'Active & Unactive User',
            'description' => 'Allow admin user to active or inactive for online & offline store user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 96,
            'permission_group' => 'Stores',
            'display_sorting' => 90.000,
            'permission_name' => 'storeuserbatchstoreactive',
            'display_name' => 'Active Batch Store User ',
            'description' => 'Allow admin user to active a batch of online & offline store user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 97,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.210,
            'permission_name' => 'merchantonlineeditcommission',
            'display_name' => 'Edit Merchant Commission',
            'description' => 'Allow admin user to edit online & offline merchant commission',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 98,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.220,
            'permission_name' => 'merchantonlineeditmerchantplatformcharge',
            'display_name' => 'Edit Merchant Platform Charge',
            'description' => 'Allow admin user to edit online & offline merchant platform charge',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 99,
            'permission_group' => 'Merchants',
            'display_sorting' => 71.230,
            'permission_name' => 'merchantonlineeditmerchantservicecharge',
            'display_name' => 'Edit Merchant Service Charge',
            'description' => 'Allow admin user to edit online & offline merchant service charge',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 100,
            'permission_group' => 'Transactions',
            'display_sorting' => 57.100,
            'permission_name' => 'transactiononlineacceptmultipleorder',
            'display_name' => 'Accept Multiple Transaction Online Order',
            'description' => 'Allow admin user to accept multiple transaction online order',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 101,
            'permission_group' => 'Administrator User',
            'display_sorting' => 2.000,
            'permission_name' => 'adminmanageuserlist',
            'display_name' => 'View Admin User',
            'description' => 'Allow admin user to view admin user list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 102,
            'permission_group' => 'Administrator User',
            'display_sorting' => 2.100,
            'permission_name' => 'adminmanageusercreate',
            'display_name' => 'Create New Admin User',
            'description' => 'Allow admin user to create new admin user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 103,
            'permission_group' => 'Administrator User',
            'display_sorting' => 2.200,
            'permission_name' => 'adminmanageuseredit',
            'display_name' => 'Edit Admin User',
            'description' => 'Allow admin user to edit existing admin user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 104,
            'permission_group' => 'Administrator User',
            'display_sorting' => 2.300,
            'permission_name' => 'adminmanageuserresetpassword',
            'display_name' => 'Admin User Reset Password',
            'description' => 'Allow admin user to reset admin user password',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 105,
            'permission_group' => 'Administrator User',
            'display_sorting' => 2.400,
            'permission_name' => 'adminmanageuserlock',
            'display_name' => 'Lock & Unlock Admin User',
            'description' => 'Allow admin user to lock or unlock admin user',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 106,
            'permission_group' => 'Transactions Offline',
            'display_sorting' => 61.100,
            'permission_name' => 'transactionofflineorderslistexport',
            'display_name' => 'Export Transaction Offline Orders',
            'description' => 'Allow admin user to export transaction offline order list & detail',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 107,
            'permission_group' => 'Transactions - Bank Info Request',
            'display_sorting' => 68.000,
            'permission_name' => 'transactionbankinforequest',
            'display_name' => 'View Transaction Bank Info Request',
            'description' => 'Allow admin user to view transaction bank info request',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 108,
            'permission_group' => 'Stores',
            'display_sorting' => 83.100,
            'permission_name' => 'storepaymentgateway',
            'display_name' => 'Edit Store Payment Gateway',
            'description' => 'Allow admin user to edit store payment gateway',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 111,
            'permission_group' => 'Transactions - Bank Info Request',
            'display_sorting' => 68.100,
            'permission_name' => 'transactionbankinforequeststatus',
            'display_name' => 'Transaction Bank Info Request Approval',
            'description' => 'Allow admin user to approve or decline the bank info request',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 112,
            'permission_group' => 'Report',
            'display_sorting' => 91.100,
            'permission_name' => 'viewsalesreport',
            'display_name' => 'View Sales Report',
            'description' => 'Allow admin user to view sales report',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 113,
            'permission_group' => 'Report',
            'display_sorting' => 91.120,
            'permission_name' => 'exportsalesreport',
            'display_name' => 'Export Sales Report',
            'description' => 'Allow admin user to export sales report',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 114,
            'permission_group' => 'Report',
            'display_sorting' => 91.200,
            'permission_name' => 'viewmicreditsummary',
            'display_name' => 'View Mi-Credit Summary',
            'description' => 'Allow admin user to view Mi-Credit summary',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 115,
            'permission_group' => 'Report',
            'display_sorting' => 91.210,
            'permission_name' => 'exportmicreditsummary',
            'display_name' => 'Export Mi-Credit Summary',
            'description' => 'Allow admin user to export Mi-Credit summary',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 116,
            'permission_group' => 'Report',
            'display_sorting' => 91.300,
            'permission_name' => 'viewmicreditlog',
            'display_name' => 'View Mi-Credit Log',
            'description' => 'Allow admin user to view Mi-Credit log',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 117,
            'permission_group' => 'Report',
            'display_sorting' => 91.310,
            'permission_name' => 'exportmicreditlog',
            'display_name' => 'Export Mi-Credit Log',
            'description' => 'Allow admin user to export Mi-Credit log',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 118,
            'permission_group' => 'Administrator Role',
            'display_sorting' => 1.300,
            'permission_name' => 'adminmanagedelete',
            'display_name' => 'Delete User Role',
            'description' => 'Allow admin user to delete user role',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 119,
            'permission_group' => 'Stores',
            'display_sorting' => 83.200,
            'permission_name' => 'storelimit',
            'display_name' => 'Store Limit',
            'description' => 'Allow admin user to view store limit',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 120,
            'permission_group' => 'Stores',
            'display_sorting' => 83.210,
            'permission_name' => 'storelimitcreate',
            'display_name' => 'Create New Store Limit',
            'description' => 'Allow admin user to create new store limit',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 121,
            'permission_group' => 'Stores',
            'display_sorting' => 83.220,
            'permission_name' => 'storelimitedit',
            'display_name' => 'Edit Store Limit',
            'description' => 'Allow admin user to edit existing store limit',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        DB::table('permission')->insert([
            'id' => 122,
            'permission_group' => 'Stores',
            'display_sorting' => 83.230,
            'permission_name' => 'storelimitdelete',
            'display_name' => 'Delete Store Limit',
            'description' => 'Allow admin user to delete store limit',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
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
