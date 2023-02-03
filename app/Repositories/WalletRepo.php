<?php

namespace App\Repositories;
use App\Models\Wallet;
use App\Models\CustomerWallet;
use App\Models\ProductCategory;
use App\Models\Category;

class WalletRepo
{
    public static function get_wallet_all()
    {
        return Wallet::where('percentage','>',0)->get();
    }

    public static function get_wallet_all_ignore_percentage()
    {
        return Wallet::orderBy('id','asc')->get();
    }

    public static function update_wallet_flat_rate($rate_value)
    {
        return Wallet::query()->update([
            'percentage' => $rate_value,
        ]);
    }

    public static function add($data)
    {
        return Wallet::create([
            'name_en' => $data['name_en'][0],
            'name_cn' => $data['name_cn'][0],
            'name_my' => $data['name_my'][0],
            'percentage' => $data['percentage'][0],
        ]);
    }

    public static function update($data)
    {
        $wallets = Wallet::all();
        foreach ($wallets as $wallet) {
            $wallet->name_en = $data['name_en'][$wallet->id];
            $wallet->name_cn = $data['name_cn'][$wallet->id];
            $wallet->name_my = $data['name_my'][$wallet->id];
            $wallet->percentage = $data['percentage'][$wallet->id];
            $wallet->save();
        }

        return true;
    }

    public static function get_wallet_id_by_product_id_based_on_product_category($product_id)
    {
        $category = ProductCategory::
        select(\DB::raw("CASE WHEN (wallet_id is null) THEN SUBSTRING_INDEX(parent_list, ',', 1) ELSE category_id END AS category_id"))
        ->leftJoin('categories','nm_product_category.category_id','=','categories.id')
        ->where('nm_product_category.product_id','=', $product_id)
        ->orderBy('nm_product_category.id')
        ->first();

        if(!empty($category))
        {
            $wallet = Category::select('wallet_id')->where('id', $category->category_id)->first();
            return intval((!empty($wallet->wallet_id))? $wallet->wallet_id : 2);
        }

        return 2;
    }

    public static function get_customer_wallet_value($cus_id, $wallet_id)
    {
        return CustomerWallet::where('customer_id', $cus_id)->where('wallet_id', $wallet_id)->value('vcredit');
    }

    public static function find_wallet($wallet_id)
    {
        return Wallet::find($wallet_id);
    }

    public static function get_wallet_online_category()
    {
        return Wallet::where('id', 1)->get();
    }

    public static function get_wallet_offline_category()
    {
        return Wallet::where('id', '<>', 1)->get();
    }
}