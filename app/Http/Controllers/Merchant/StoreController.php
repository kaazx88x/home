<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Merchant\Controller;
use App\Repositories\StoreRepo;
use App\Repositories\CityRepo;
use App\Repositories\CountryRepo;
use App\Repositories\S3ClientRepo;
use App\Repositories\MerchantRepo;
use App\Repositories\StoreUserRepo;
use App\Repositories\LimitRepo;

class StoreController extends Controller
{

    public function __construct(StoreRepo $storerepo, CityRepo $cityrepo, CountryRepo $countryrepo)
    {
        $this->store = $storerepo;
        $this->city = $cityrepo;
        $this->country = $countryrepo;
    }

    public function manage()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $input = \Request::only('id', 'name', 'type', 'status', 'sort');

        $stores = $this->store->get_stores_by_merchant($mer_id, $input);

        foreach($stores as $store) {
            $store['store_user'] = StoreUserRepo::get_store_users_list($store['stor_id'], $store['stor_merchant_id'])->where('assigned', 1);
        }

        return view('merchant.store.manage', compact('stores', 'input'));
    }

    public function add()
    {
        if(\Auth::guard('merchants')->check()) {
            $mer_id = \Auth::guard('merchants')->user()->mer_id;
        }

        if(\Auth::guard('storeusers')->check()) {
            $mer_id = \Auth::guard('storeusers')->user()->mer_id;
        }

        $mer_type = \Auth::guard('merchants')->user()->mer_type;
        $stores = $this->store->get_stores_by_merchant($mer_id);
        // if  (count($stores) != 0)
        //     return redirect('merchant/store/manage')->with('error', 'Sorry. Only one store can be added for each merchant.');

        $cities = $this->city->get_cities();
        $countries = ($mer_type == 0)? $this->country->get_all_countries() : $this->country->get_all_offline_ountries();

        $users = StoreUserRepo::get_store_users_list($store_id = null, $mer_id);

        return view('merchant.store.add', compact('cities', 'countries','users','mer_id','users','mer_type'));
    }

    public function add_submit()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $merchant = MerchantRepo::get_merchant($mer_id);

        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $v = \Validator::make($data, [
                // 'stor_type' => 'required',
                'stor_name' => 'required',
                'stor_phone' => 'required|numeric',
                'stor_office_number' => 'required|numeric',
                'stor_address1' => 'required',
                'stor_city_name' => 'required',
                'stor_country' => 'required',
                'stor_state' => 'required',
                // 'stor_metakeywords' => 'required',
                // 'stor_metadesc' => 'required',
                'stor_website' => 'nullable|url',
                'stor_img' => 'required|mimes:jpeg,jpg,png|max:1000',
                'stor_zipcode' => 'required|numeric',
                'latitude' => $merchant->mer_type == 1? 'required' : '',
                'longtitude' => $merchant->mer_type == 1? 'required' : '',
	        ],[
                'stor_website.url'=> trans('localize.website_url_error'),
            ]);

            if ($v->fails())
               return back()->withInput()->withErrors($v);

            //store categories for offline store
            if ($merchant->mer_type > 0) {
                $store_categories = json_decode($data['selected_cats']);
                if(empty($store_categories)) {
                    return back()->withInput()->withErrors('Store categories is required');
                }
            }

            // Upload Image
            $store_image = '';
            if (!empty($data['stor_img'])) {
                $upload_file = $data['stor_img']->getClientOriginalName();
                $file_detail = explode('.', $upload_file);
                $store_image = date('Ymd').'_'.str_random(4).'.'.$file_detail[1];
                //$move_file = $data['stor_img']->move(public_path('images/store/'.$mer_id.'/'), $store_image);
                $path = 'store/'.$mer_id;
                    if(@file_get_contents($data['stor_img']) && !S3ClientRepo::IsExisted($path, $store_image))
                        $upload = S3ClientRepo::Upload($path, $data['stor_img'], $store_image);
            }

            try {
                $data['stor_type'] = $merchant->mer_type;
                $store = $this->store->add_store($data, $mer_id, $store_image);

                if(isset($data['store_users']))
                    $update = StoreUserRepo::update_store_user_mapping($data['store_users'], $store->stor_id);

                if ($merchant->mer_type > 0) {
                    $store_id = $store->stor_id;
                    foreach ($store_categories as $key => $cat) {
                        if ($cat) {
                            $store_category = array(
                                'store_id' => $store_id,
                                'offline_category_id' => $cat,
                            );

                            try {
                                StoreRepo::insert_offline_category_details($store_category);
                            } catch (\Exception $e) {
                                return back()->withInput()->withErrors('Unable to add store categories');
                            }
                        }
                    }
                }

            } catch (\Exception $e) {
                logger()->error($e);
                return back()->withInput()->withErrors('Unable to add store. Please try again.');
            }

            return redirect('merchant/store/manage')->with('success', trans('localize.successaddstore2', ['storename' => $data['stor_name']]));
        }
    }

    public function edit($store_id)
    {
        if(\Auth::guard('merchants')->check()) {
            $mer_id = \Auth::guard('merchants')->user()->mer_id;
        }

        if(\Auth::guard('storeusers')->check()) {
            $mer_id = \Auth::guard('storeusers')->user()->mer_id;
        }

        $store = $this->store->get_store_by_id($store_id);
        if(empty($store))
            return redirect('merchant/store/manage')->with('error', trans('localize.store_not_found'));

        if (str_is('http://*', $store->stor_website)) {
            $store->mer_http = "http://";
            $store->stor_website = str_replace("http://", '', $store->stor_website);
        } else {
            $store->mer_http = "https://";
            $store->stor_website = str_replace("https://", '', $store->stor_website);
        }

        $countries = ($store->stor_type == 0)? $this->country->get_all_countries() : $this->country->get_all_offline_ountries();
        $images = $this->store->get_images($store_id);
        $categories = $this->store->get_offline_categories($store_id);
        $store_category_list = [];
        $users = StoreUserRepo::get_store_users_list($store_id, $mer_id);
        $reviews = $this->store->get_merchant_reviews($store_id);
        foreach ($categories as $key => $category) {
            $store_category_list[] = strval($category['details']->offline_category_id);
        }

        return view('merchant.store.edit', compact('countries', 'store', 'images', 'users', 'categories', 'store_category_list', 'reviews'));
    }

     public function edit_submit($store_id)
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $store = StoreRepo::get_store_by_id($store_id);

        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $v = \Validator::make($data, [
                // 'stor_type' => 'required',
                // 'stor_name' => 'required',
                'stor_phone' => 'required|numeric',
                'stor_office_number' => 'required|numeric',
                'stor_address1' => 'required',
                // 'stor_address2' => 'required',
                'stor_state' => 'required',
                'stor_country' => 'required',
                // 'longtitude' => 'required',
                // 'latitude' => 'required',
                // 'stor_metakeywords' => 'required',
                // 'stor_metadesc' => 'required',
                // 'stor_website' => 'required',
                'stor_city_name' => 'required',
                'stor_img' => 'mimes:jpeg,jpg,png|max:1000',
                // 'file' => 'mimes:jpeg,jpg,png|max:1000',
                'stor_zipcode' => 'required|numeric',
                'latitude' => $store->stor_type == 1? 'required' : '',
                'longtitude' => $store->stor_type == 1? 'required' : '',
	        ]);

            if ($v->fails())
               return back()->withInput()->withErrors($v);


            //store categories for offline store
            if ($store->stor_type > 0) {
                $store_categories = json_decode($data['selected_cats']);
                if(empty($store_categories)) {
                    return back()->withInput()->withErrors('Store categories is required');
                }
            }

            // delete image gallery
            if (isset($data['file_old_id'])) {
                StoreRepo::delete_gallery_ifNotIn($data['file_old_id'], $store_id);
            }

            if (isset($data['file'])) {
                foreach ($data['file'] as $key => $file) {
                     $v = \Validator::make($data, [
                        'file'.$key => 'mimes:jpeg,jpg,png|max:1000',
                    ]);

                    if ($v->fails())
                    return back()->withInput()->withErrors($v);
                }
                // Upload image gallery
                $img = '';
                $img_count = 0;
                foreach ($data['file'] as $key => $file) {
                    if (!empty($file)) {
                        // Here should check to delete image that be replaced
                        $img_count++;
                        $upload_file = $file->getClientOriginalName();
                        $file_detail = explode('.', $upload_file);
                        $image_name = date('Ymd').'_'.str_random(4).'.'.$file_detail[1];
                        //$move_file = $file->move(public_path('images/store/'.$mer_id.'/'), $image_name);
                        $path = 'store/'.$mer_id;
                            if(@file_get_contents($file) && !S3ClientRepo::IsExisted($path, $image_name))
                                $upload = S3ClientRepo::Upload($path, $file, $image_name);

                        // save|update in gallery
                        if (!empty($data['file_old'][$key])) {
                            $update = $this->store->update_image($data['file_old_id'][$key], $image_name);
                        } else {
                            $add = $this->store->add_image($store_id, $image_name);
                        }
                    }
                }
            }

            // Upload Image
            $store_image = $data['stor_img_old'];
            if (isset($data['stor_img'])) {
                $upload_file = $data['stor_img']->getClientOriginalName();
                $file_detail = explode('.', $upload_file);
                $store_image = date('Ymd').'_'.str_random(4).'.'.$file_detail[1];
                //$move_file = $data['stor_img']->move(public_path('images/store/'.$mer_id.'/'), $store_image);
                $path = 'store/'.$mer_id;
                    if(@file_get_contents($data['stor_img']) && !S3ClientRepo::IsExisted($path, $store_image))
                        $upload = S3ClientRepo::Upload($path, $data['stor_img'], $store_image);
            }

            try {
                $store = $this->store->update_store($data, $store_image, $store_id);

                if(isset($data['store_users']))
                    $update = StoreUserRepo::update_store_user_mapping($data['store_users'], $store->stor_id);

                if(isset($data['store_users']) == false && $data['exist'] == 1)
                    $remove = StoreUserRepo::remove_store_user_mapping($store->stor_id);

                if ($store->stor_type > 0) {
                    $store_id = $store->stor_id;

                    // Check got changes or not

                    $old_main_category = StoreRepo::get_store_main_category($store_id);
                    $review = false;

                    if (!empty($data['selected_cats'])) {
                        $store_categories = json_decode($data['selected_cats']);

                        // Update offline Category
                        StoreRepo::delete_offline_category($store_id);
                        foreach ($store_categories as $key => $cat) {
                            if ($cat) {
                                // if($old_main_category && ($key == 0) && ($cat != $old_main_category) && ($store->stor_status == 1))
                                //     $review = true;

                                $store_category = array(
                                    'store_id' => $store_id,
                                    'offline_category_id' => $cat,
                                );

                                try {
                                    StoreRepo::insert_offline_category_details($store_category);
                                } catch (\Exception $e) {
                                    return back()->withInput()->withErrors(trans('localize.failupdatestore'));
                                }
                            }
                        }
                    }

                    if($review) {
                        StoreRepo::update_store_status($store_id, 2);
                    }
                }

            } catch (\Exception $e) {
                return back()->withInput()->withErrors(trans('localize.failupdatestore'));
            }

            return back()->with('success', trans('localize.successupdatestore'));
        }
    }

    public function limit($store_id)
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $store = StoreRepo::find_store($mer_id, $store_id);

        if(!$store || $store->stor_type == 0 || !$store->accept_payment)
            return redirect('merchant/store/manage')->with('error', trans('localize.invalid_request'));

        if(!$store->limit)
            $store = LimitRepo::init_store_limit($store_id);

        for ($type=2; $type <= 5; $type++) {
            switch ($type) {
                case 2:
                    $current_amount = $store->limit->daily;
                    $current_count = $store->limit->daily_count;
                    break;

                case 3:
                    $current_amount = $store->limit->weekly;
                    $current_count = $store->limit->weekly_count;
                    break;

                case 4:
                    $current_amount = $store->limit->monthly;
                    $current_count = $store->limit->monthly_count;
                    break;

                case 5:
                    $current_amount = $store->limit->yearly;
                    $current_count = $store->limit->yearly_count;
                    break;
            }

            $transactions[$type] = [
                'current_amount' => $current_amount,
                'block_amount' => false,
                'limit_amount_exceed' => 0,
                'limit_amount_usage' => 0,

                'current_count' => $current_count,
                'block_count' => false,
                'limit_count_exceed' => 0,
                'limit_count_usage' => 0,
            ];

            if(!$store->limit->blocked->isEmpty()) {
                $block = $store->limit->store_blocked->where('type', '=', $type)->first();
                if($block) {
                    if($block->amount > 0) {
                        $transactions[$type]['block_amount'] = true;
                        $transactions[$type]['limit_amount_exceed'] = $block->amount;
                        $transactions[$type]['limit_amount_usage'] = round(($transactions[$type]['current_amount'] / $block->amount) * 100, 2);
                    }

                    if($block->number_transaction > 0) {
                        $transactions[$type]['block_count'] = true;
                        $transactions[$type]['limit_count_exceed'] = $block->number_transaction;
                        $transactions[$type]['limit_count_usage'] = round(($transactions[$type]['current_count'] / $block->number_transaction) * 100, 2);
                    }
                }
            }
        }

        $types = [
            '1' => trans('localize.single_limit'),
            '2' => trans('localize.daily_limit'),
            '3' => trans('localize.weekly_limit'),
            '4' => trans('localize.monthly_limit'),
            '5' => trans('localize.yearly_limit'),
        ];

        $actions = [
            '1' => trans('localize.alert'),
            '2' => trans('localize.Block'),
        ];

        return view('merchant.store.limit', compact('store', 'types', 'actions', 'transactions'));
    }

}
