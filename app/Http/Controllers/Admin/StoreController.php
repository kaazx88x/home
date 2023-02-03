<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Controller;
use App\Repositories\StoreRepo;
use App\Repositories\CityRepo;
use App\Repositories\CountryRepo;
use App\Repositories\S3ClientRepo;
use App\Repositories\MerchantRepo;
use App\Repositories\StoreUserRepo;
use App\Repositories\LimitRepo;

class StoreController extends Controller
{
    public function store_list()
    {
        $mer_id = '';
        $review = true;
        $input['review'] = true;
        $stores = StoreRepo::get_all_store($input);

        return view('admin.store.manage', compact('stores','mer_id','review'));
    }

    public function manage($merid_type)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		

        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);
        $block_store_permission = in_array('storesetstatus', $admin_permission);
        $toggle_listed_permission = in_array('storetogglelisted', $admin_permission);
        $batch_store_active_permission = in_array('storeuserbatchstoreactive', $admin_permission);
		$limit = in_array('storelimit', $admin_permission);

        $input = \Request::only('id', 'name', 'mid', 'mname', 'type', 'status', 'sort');
        $input['admin_country_id_list'] = $admin_country_id_list;
        $review = ($input['status'] == 2)? true : false;
        $mer_id = null;
        $pending = false;

        if(is_numeric($merid_type)) {
            $mer_id = $merid_type;
        } else {
            $type = $merid_type;
            switch ($type) {
                case 'online':
                    $type = 0;
                    $input['type'] = 0;
                    break;

                case 'offline':
                    $type = 1;
                    $input['type'] = 1;
                    break;

                case 'pending':
                    $type = 2;
                    $pending = true;
                    $input['status'] = 2;
                    break;

                default:
                    return redirect('/admin/merchant/manage')->with('error', trans('localize.Invalid_store_type'));
                    break;
            }
        }

        if($mer_id) {
            $merchant = MerchantRepo::find_merchant($mer_id);
            if(!$merchant)
                return back('/admin/merchant/manage')->with('error', trans('localize.Merchant_not_available'));

            $type = $merchant->mer_type;
            $stores = StoreRepo::get_stores_by_merchant($mer_id,$input);

        } else {
            $stores = StoreRepo::get_stores_by_merchant(null, $input);
        }

        return view('admin.store.manage', compact('limit','stores','mer_id','type','review','input','merid_type', 'pending','batch_store_active_permission','toggle_listed_permission','block_store_permission'));
    }

    public function add($mer_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $mer_type = MerchantRepo::get_merchant($mer_id)->mer_type;
        $cities = CityRepo::get_cities();
        $countries = ($mer_type == 0)? CountryRepo::get_countries($admin_country_id_list) : CountryRepo::get_offline_ountries($admin_country_id_list);
        $users = StoreUserRepo::get_store_users_list($store_id = null, $mer_id);

        return view('admin.store.add', compact('mer_id','cities','countries','mer_id','users','mer_type'));
    }

    public function add_submit($mer_id)
    {
        $merchant = MerchantRepo::get_merchant($mer_id);
        if(!$merchant)
            return back()->withInput()->with('error', trans('localize.invalid_operation'));

        if (\Request::isMethod('post')) {
            $data = \Request::all();

            $v = \Validator::make($data, [
                // 'stor_type' => 'required',
                'stor_name' => 'required',
                'stor_phone' => 'required|numeric',
                'stor_address1' => 'required',
                'stor_city_name' => 'required',
                'stor_country' => 'required',
                'stor_state' => 'required',
                'stor_zipcode' => 'required|numeric',
                // 'stor_metakeywords' => 'required',
                // 'stor_metadesc' => 'required',
                'stor_website' => 'nullable|url',
                'stor_img' => 'required|mimes:jpeg,jpg,png|max:1000',
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
                    return back()->withInput()->withErrors(trans('localize.Store_categories_is_required'));
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
                $data['by_admin'] = true;
                $store = StoreRepo::add_store($data, $mer_id, $store_image);

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
                                return back()->withInput()->withErrors(trans('localize.Unable_to_add_store_categories'));
                            }
                        }
                    }
                }

            } catch (\Exception $e) {
                return back()->withInput()->withErrors('Unable to add store. Please try again.');
            }

            return redirect('admin/store/manage/'.$mer_id)->with('success', trans('localize.successaddstore'));
        }
    }

    public function edit($mer_id,$store_id)
    {
        $adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
        $edit_permission = in_array('storeedit', $admin_permission);
        $admin_country_id_list = Controller::getAdminCountryIdList($adm_id);

        $store = StoreRepo::get_store_by_id($store_id);

        $store_country_id = isset($store['stor_country']) ? $store['stor_country'] : 0;
        $admin_country_id_list = array_merge($admin_country_id_list, array(0));

        $check_validation = in_array($store_country_id, $admin_country_id_list);
        if(!$check_validation){
            return redirect('admin')->with('denied', 'You are not authorized to access that page');
        }

        $countries = ($store->stor_type == 0)? CountryRepo::get_countries($admin_country_id_list) : CountryRepo::get_offline_ountries($admin_country_id_list);
        $images = StoreRepo::get_images($store_id);
        $categories = StoreRepo::get_offline_categories($store_id);
        $users = StoreUserRepo::get_store_users_list($store_id, $mer_id);
        $store_category_list = [];
        foreach ($categories as $key => $category) {
            $store_category_list[] = strval($category['details']->offline_category_id);
        }

        return view('admin.store.edit', compact('cities','countries','store', 'images', 'users', 'categories', 'store_category_list', 'edit_permission'));
    }

    public function edit_submit($mer_id,$store_id)
    {
        $merchant = MerchantRepo::get_merchant($mer_id);
        $store = StoreRepo::get_store_by_id($store_id);
        if(!$merchant || !$store)
            return back()->withInput()->with('error', trans('localize.invalid_operation'));

        if (\Request::isMethod('post')) {
            $data = \Request::all();
            $v = \Validator::make($data, [
                // 'stor_type' => 'required',
                'stor_name' => 'required',
                'stor_phone' => 'required|numeric',
                'stor_address1' => 'required',
                // 'stor_address2' => 'required',
                'stor_city_name' => 'required',
                'stor_country' => 'required',
                // 'longtitude' => 'required',
                // 'latitude' => 'required',
                'stor_img' => 'mimes:jpeg,jpg,png|max:1000',
                'stor_zipcode' => 'required|numeric',
                'latitude' => $merchant->mer_type == 1? 'required' : '',
                'longtitude' => $merchant->mer_type == 1? 'required' : '',
	        ]);

            if ($v->fails())
               return back()->withInput()->withErrors($v);

            //store categories for offline store
            if ($store->stor_type > 0) {
                $store_categories = json_decode($data['selected_cats']);
                if(empty($store_categories)) {
                    return back()->withInput()->withErrors(trans('localize.Store_categories_is_required'));
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
                            $update = StoreRepo::update_image($data['file_old_id'][$key], $image_name);
                        } else {
                            $add = StoreRepo::add_image($store_id, $image_name);
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

            // try {
                $store = StoreRepo::update_store($data, $store_image, $store_id);

                if(isset($data['store_users']))
                    $update = StoreUserRepo::update_store_user_mapping($data['store_users'], $store->stor_id);

                if(isset($data['store_users']) == false && $data['exist'] == 1)
                    $remove = StoreUserRepo::remove_store_user_mapping($store->stor_id);

            // } catch (\Exception $e) {
            //     return back()->withInput()->withErrors(trans('localize.failupdatestore'));
            // }

            if (!empty($data['selected_cats'])) {
                $cats = json_decode($data['selected_cats']);
                StoreRepo::delete_offline_category($store_id);
                foreach ($cats as $key => $cat) {
                    if ($cat) {
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

            return back()->with('success', trans('localize.successupdatestore'));
        }
    }

    public function update_store_status($store_id,$status)
    {
        $store = StoreRepo::get_store_by_id($store_id);
        if(!$store || ($status == 1 && $store->mer_staus != 1))
            return back()->with('error', trans('invalid_request'));

        $update = StoreRepo::update_store_status($store_id, $status);

        if($status == 1)
            return back()->with('success', trans('localize.Successfully_set_store_to_active'));
        else
            return back()->with('success', trans('localize.Successfully_set_store_to_inactive'));

    }

    public function update_store_listed_status($store_id)
    {
        $update = StoreRepo::update_store_listed_status($store_id);

        return back()->with('success', trans('localize.Successfully_update_store_listed'));

    }

    public function batch_status_update($operation)
    {
        if (\Request::isMethod('post')) {
            $data = \Request::all();

            if(!isset($data['store_id']))
                return back()->withError(trans('localize.Nothing_to_be_updated'));

            StoreRepo::batch_status_update($operation, $data['store_id']);

            return back()->withSuccess(trans('localize.Successfully_set_store_to_active'));
        }
    }

    public function limit($mer_id, $store_id)
    {
		$adm_id = \Auth::guard('admins')->user()->adm_id;
        $admin_permission = Controller::adminPermissionList($adm_id);
		
		if(in_array('storelimit', $admin_permission) == false){
		return redirect('admin')->with('denied', trans('localize.You_are_not_authorized_to_access_that_page'));
		}
		
        $add = in_array('storelimitcreate', $admin_permission);
        $edit = in_array('storelimitedit', $admin_permission);
		$delete = in_array('storelimitdelete', $admin_permission);
		
        $store = StoreRepo::find_store($mer_id, $store_id);
        if(!$store || $store->stor_type == 0)
            return redirect('admin/store/manage/'.$mer_id)->with('error', 'Invalid request');

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

            if(!$store->limit->store_blocked->isEmpty()) {
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

        return view('admin.store.limit', compact('add','edit','delete','store', 'types', 'actions', 'transactions','create_permission', 'edit_permission','delete_permission'));
    }

    public function limit_create($mer_id, $store_id, $limit_id)
    {
        $store = StoreRepo::find_store($mer_id, $store_id);
        if(!$store || $store->stor_type == 0)
            return redirect('admin/store/manage/'.$mer_id)->with('error', trans('localize.invalid_request'));

        $data = \Request::all();
        \Validator::make($data, [
            'type' => 'required|in:1,2,3,4,5',
            'action' => 'required|in:1,2',
            'amount' => 'nullable|required_if:type,1|numeric|min:1',
            'number_transaction' => 'nullable|integer|min:1',
        ])->validate();

        if(isset($data['per_user']) && $data['action'] == 2) {
            $data['per_user'] = 1;
        } else {
            $data['per_user'] = 0;
        }

        if(empty($data['amount']) && empty($data['number_transaction'])) {
            return back()->with('error', 'Please insert either amount or number of transaction');
        }

        if(!$store->limit->actions->isEmpty() && $store->limit->actions->where('type', $data['type'])->where('action', $data['action'])->count() > 0)
            return back()->with('error', trans('localize.Selected_limit_and_action_already_exist'));

        //check limit block sequence
        if($data['type'] > 1 && $data['action'] == 2 && !$store->limit->store_blocked->isEmpty()) {
            $types = [
                '1' => 'Single Limit',
                '2' => 'Daily Limit',
                '3' => 'Weekly Limit',
                '4' => 'Monthly Limit',
                '5' => 'Yearly Limit',
            ];

            if(!empty($data['amount'])) {
                $min = $store->limit->store_blocked->where('type', '<', $data['type'])->where('amount', '>', 0)->sortByDesc('type')->first();
                $max = $store->limit->store_blocked->where('type', '>', $data['type'])->where('amount', '>', 0)->sortBy('type')->first();

                if($min && $min->type > 1 && $data['amount'] < $min->amount) {
                    return back()->with('error', 'Amount must higher than '.$types[$min->type].' value, Minimum amount is '.$min->amount);
                }

                if($max && $max->type > 1 && $data['amount'] > $max->amount) {
                    return back()->with('error', 'Amount must less than '.$types[$max->type].' value, Maximum amount is '.$max->amount);
                }
            }

            if(!empty($data['number_transaction'])) {
                $min = $store->limit->store_blocked->where('type', '<', $data['type'])->where('number_transaction', '>', 0)->sortByDesc('type')->first();
                $max = $store->limit->store_blocked->where('type', '>', $data['type'])->where('number_transaction', '>', 0)->sortBy('type')->first();

                if($min && $min->type > 1 && $data['number_transaction'] < $min->number_transaction) {
                    return back()->with('error', 'Number transaction must higher than '.$types[$min->type].' value, Minimum number of transaction is '.$min->number_transaction);
                }

                if($max && $max->type > 1 && $data['number_transaction'] > $max->number_transaction) {
                    return back()->with('error', 'Number transaction must less than '.$types[$max->type].' value, Maximum number of transaction is '.$max->number_transaction);
                }
            }
        }

        LimitRepo::add_action($limit_id, $data);

        return back()->with('success', trans('localize.Successfully_created_new_limit'));
    }

    public function limit_edit($mer_id, $store_id, $action_id)
    {
        $store = StoreRepo::find_store($mer_id, $store_id);
        if(!$store || $store->stor_type == 0)
            return redirect('admin/store/manage/'.$mer_id)->with('error', trans('localize.invalid_request'));

        $action = $store->limit->actions->keyBy('id')->get($action_id);

        return view('modals.edit_store_limit', compact('mer_id', 'store_id', 'action_id', 'action'))->render();
    }

    public function limit_edit_submit($mer_id, $store_id, $action_id)
    {
        $store = StoreRepo::find_store($mer_id, $store_id);
        if(!$store || $store->stor_type == 0)
            return redirect('admin/store/manage/'.$mer_id)->with('error', trans('localize.invalid_request'));

        $action = $store->limit->actions->keyBy('id')->get($action_id);
        if(!$action)
            return back()->with('error', 'Invalid request');

        $data = \Request::all();
        \Validator::make($data, [
            'amount' => ($action->type == 1)? 'required|numeric|min:1' : 'nullable|numeric|min:1',
            'number_transaction' => 'nullable|integer|min:1',
        ])->validate();

        if(isset($data['per_user']) && $action->action == 2) {
            $data['per_user'] = 1;
        } else {
            $data['per_user'] = 0;
        }

        if($action->type > 1 && empty($data['amount']) && empty($data['number_transaction'])) {
            return back()->with('error', 'Please insert either amount or number of transaction');
        }

        //check limit block sequence
        if($action->type > 1 && $action->action == 2 && !$store->limit->blocked->isEmpty()) {
            $types = [
                '1' => 'Single Limit',
                '2' => 'Daily Limit',
                '3' => 'Weekly Limit',
                '4' => 'Monthly Limit',
                '5' => 'Yearly Limit',
            ];

            if(!empty($data['amount'])) {
                $min = $store->limit->blocked->where('type', '<', $action->type)->where('amount', '>', 0)->sortByDesc('type')->first();
                $max = $store->limit->blocked->where('type', '>', $action->type)->where('amount', '>', 0)->sortBy('type')->first();

                if($min && $min->type > 1 && $data['amount'] < $min->amount) {
                    return back()->with('error', 'Amount must higher than '.$types[$min->type].' value, Minimum amount is '.$min->amount);
                }

                if($max && $max->type > 1 && $data['amount'] > $max->amount) {
                    return back()->with('error', 'Amount must less than '.$types[$max->type].' value, Maximum amount is '.$max->amount);
                }
            }

            if(!empty($data['number_transaction'])) {
                $min = $store->limit->blocked->where('type', '<', $action->type)->where('number_transaction', '>', 0)->sortByDesc('type')->first();
                $max = $store->limit->blocked->where('type', '>', $action->type)->where('number_transaction', '>', 0)->sortBy('type')->first();

                if($min && $min->type > 1 && $data['number_transaction'] < $min->number_transaction) {
                    return back()->with('error', 'Number transaction must higher than '.$types[$min->type].' value, Minimum number of transaction is '.$min->number_transaction);
                }

                if($max && $max->type > 1 && $data['number_transaction'] > $max->number_transaction) {
                    return back()->with('error', 'Number transaction must less than '.$types[$max->type].' value, Maximum number of transaction is '.$max->number_transaction);
                }
            }
        }

        $update = LimitRepo::edit_action($action->id, $data);

        return back()->with('success', trans('localize.Successfully_updated_limit_action'));
    }

    public function limit_action_delete($mer_id, $store_id, $action_id)
    {
        $store = StoreRepo::find_store($mer_id, $store_id);
        if(!$store || $store->stor_type == 0)
            return redirect('admin/store/manage/'.$mer_id)->with('error', trans('localize.invalid_request'));

        $action = $store->limit->actions->keyBy('id')->get($action_id);
        if(!$action)
            return back()->with('error', trans('localize.invalid_request'));

        $delete = LimitRepo::delete_action($action_id);

        return back()->with('success', trans('localize.Successfully_deleted_limit_action'));
    }
}