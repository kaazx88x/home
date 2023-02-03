<?php

namespace App\Repositories;
use DB;
use App\Models\Store;
use App\Models\StoreRating;
use App\Models\StoreReview;
use App\Models\StoreImage;
use App\Models\StoreCategory;
use App\Models\OfflineCategory;
use App\Models\StoreUserMapping;
use App\Models\AdminSetting;
use Auth;

class StoreRepo
{
    public static function get_online_store_by_merchant_id($mer_id)
    {
        $store = Store::where('stor_merchant_id','=',$mer_id)->where('stor_type','=', 0)->where('stor_status', '=', 1)->get();
        return $store;
    }

    public static function get_online_store_by_merchant_id_and_country($mer_id, $admin_country_id_list = array())
    {
        $store = Store::where('stor_merchant_id','=',$mer_id)
                ->whereIn('stor_country',$admin_country_id_list)
                ->where('stor_type','=', 0)
                ->where('stor_status', '=', 1)
                ->get();

        return $store;
    }

    public static function get_stores_by_merchant($mer_id = null, $input = array())
    {
        $stores = Store::leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_store.stor_merchant_id')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_store.stor_city')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country');

        if (!empty($input['admin_country_id_list'])) {
            $stores->whereIn('nm_store.stor_country', $input['admin_country_id_list']);
        }

        if(!empty($mer_id))
            $stores->where('stor_merchant_id', '=', $mer_id);

        if (!empty($input['id']))
            $stores->where('nm_store.stor_id', '=', $input['id']);

        if (!empty($input['mid']))
            $stores->where('nm_store.stor_merchant_id', '=', $input['mid']);

        if (!empty($input['name']))
            $stores->where('nm_store.stor_name', 'LIKE', '%'.$input['name'].'%');

        if (!empty($input['mname'])) {
            $search = '%'.$input['mname'].'%';
            $stores->where(function($query) use ($search) {
                $query->whereRaw('nm_merchant.mer_fname LIKE ? or nm_merchant.mer_lname LIKE ?', [$search, $search]);
            });
        }

        if(isset($input['type'])) {
            if (!empty($input['type']) || $input['type'] == '0') {
                $stores->where('nm_store.stor_type', '=', $input['type']);
            }
        }

        if (isset($input['status']) && (!empty($input['status'])  || $input['status'] == '0'))
            $stores->where('nm_store.stor_status', '=', $input['status']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'name_asc':
                    $stores->orderBy('nm_store.stor_name');
                    break;
                case 'name_desc':
                    $stores->orderBy('nm_store.stor_name', 'desc');
                    break;
                case 'new':
                    $stores->orderBy('nm_store.created_at', 'desc');
                    break;
                case 'old':
                    $stores->orderBy('nm_store.created_at', 'asc');
                    break;
                default:
                    $stores->orderBy('nm_store.stor_id', 'desc');
                    break;
            }
        } else {
            $stores->orderBy('nm_store.created_at', 'desc');
        }

        return $stores->paginate(50);

    }

    public static function add_store($data, $mer_id, $img)
    {
        $added_by = 2;
        $status = 2;
        if (isset($data['by_admin']) && $data['by_admin'] && \Auth::guard('admins')->check()) {
            $added_by = 1;
            $status = 1;
        }

        $store = Store::create([
            'stor_merchant_id' => $mer_id,
            'stor_type' => $data['stor_type'],
            'stor_name' => $data['stor_name'],
            'stor_phone' => $data['stor_phone'],
            'stor_office_number' => $data['stor_office_number'],
            'stor_address1' => $data['stor_address1'],
            'stor_address2' => $data['stor_address2'],
            'stor_country' => $data['stor_country'],
            'stor_state' => $data['stor_state'],
            'stor_city_name' => $data['stor_city_name'],
            'stor_zipcode' => $data['stor_zipcode'],
            'stor_metakeywords' => $data['stor_metakeywords'],
            'stor_metadesc' => $data['stor_metadesc'],
            'stor_latitude' => $data['latitude'],
            'stor_longitude' => $data['longtitude'],
            'stor_img' => $img,
            'stor_website' => $data['stor_website'],
            'short_description' => $data['short_description'],
            'long_description' => $data['long_description'],
            'office_hour' => (isset($data['office_hour']))? $data['office_hour'] : '',
            'featured' => (isset($data['featured']))? $data['featured'] : 0,
            'accept_payment' => (isset($data['accept_payment']))? $data['accept_payment'] : 0,
            'listed' => (isset($data['listed']))? $data['listed'] : 0,
            'stor_status' => $status,
            'stor_addedby' => $added_by,
            'stor_commission' => ($data['stor_type'] == 1) ? 10 : 10,
            'listed' => (isset($data['listed']))? $data['listed'] : 0,
            'map_type' => (isset($data['map_type'])) ? $data['map_type'] : 0,
        ]);

        return $store;
    }

    public static function update_store($data, $img, $stor_id)
    {
        $store = Store::find($stor_id);
        // $store->stor_type = (isset($data['stor_type']))? $data['stor_type'] : $store->stor_type;
        $store->stor_name = (isset($data['stor_name']))? $data['stor_name'] : $store->stor_name;
        $store->stor_phone = $data['stor_phone'];
        $store->stor_office_number = $data['stor_office_number'];
        $store->stor_address1 = $data['stor_address1'];
        $store->stor_address2 = $data['stor_address2'];
        $store->stor_country = $data['stor_country'];
        $store->stor_state = $data['stor_state'];
        $store->stor_city_name = $data['stor_city_name'];
        $store->stor_zipcode = $data['stor_zipcode'];
        $store->stor_metakeywords = $data['stor_metakeywords'];
        $store->stor_metadesc = $data['stor_metadesc'];
        $store->stor_latitude = $data['latitude'];
        $store->stor_longitude = $data['longtitude'];
        $store->stor_img = $img;
        $store->stor_website = (isset($data['mer_http']) ? $data['mer_http'] : '') . $data['stor_website'];
        $store->short_description = $data['short_description'];
        $store->long_description = $data['long_description'];
        $store->office_hour = (isset($data['office_hour']))? $data['office_hour'] : '';
        $store->featured = (isset($data['featured'])) ? $data['featured'] : $store->featured;
        $store->accept_payment = (isset($data['accept_payment'])) ? $data['accept_payment'] : $store->accept_payment;
        $store->listed = (isset($data['listed'])) ? $data['listed'] : $store->listed;
        $store->map_type = $data['map_type'];
        $store->default_price = (isset($data['default_price'])) ? $data['default_price'] : $store->default_price;
        $store->save();

        return $store;
    }

    public static function get_store_by_id($store_id)
    {
        return Store::leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_store.stor_city')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_store.stor_merchant_id')
        ->where('nm_store.stor_id', '=', $store_id)
        ->first();
    }

    public static function get_store_listing($data)
    {
        $stores = Store::selectRaw(
            '*, ((((acos(sin((?*pi()/180)) * sin((`stor_latitude`*pi()/180))+cos((?*pi()/180)) * cos((`stor_latitude`*pi()/180)) * cos(((?-`stor_longitude`) * pi()/180))))*180/pi())*60*1.1515) * 1.609344) as distance', [
                $data['latitude'],
                $data['latitude'],
                $data['longitude'],
            ]
        )
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_store.stor_city')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_store.stor_merchant_id')
        ->where('stor_type', '>', 0)
        ->where('listed', '=', 1)
        ->where('stor_status', '=', 1);

        if ($data['featured'])
            $stores->where('nm_store.featured', '=', 1);

        if ($data['search'])
            $stores->where('stor_name', 'LIKE', '%'.$data['search'].'%');

        if (!empty($data['country_code'])) {
            $stores->where('nm_country.co_code', '=', trim($data['country_code']));
        } else {
            if ($data['range'])
                $stores->whereRaw(
                    '((((acos(sin((?*pi()/180)) * sin((`stor_latitude`*pi()/180))+cos((?*pi()/180)) * cos((`stor_latitude`*pi()/180)) * cos(((?-`stor_longitude`) * pi()/180))))*180/pi())*60*1.1515) * 1.609344) <= ?', [
                        trim($data['latitude']),
                        trim($data['latitude']),
                        trim($data['longitude']),
                        trim($data['range'])
                    ]
                );
        }

        if ($data['category_id']) {
            $childs = OfflineCategory::where('id', $data['category_id'])->first();
            $child_list = [];

            if ($childs->child_list)
                $child_list = explode(',', $childs->child_list);

            array_push($child_list, $data['category_id']);
            $stores->leftJoin('nm_store_offline_category', 'nm_store_offline_category.store_id', '=', 'nm_store.stor_id')
            ->whereIn('nm_store_offline_category.offline_category_id', $child_list);
        }

        if ($data['sort']) {
            switch ($data['sort']) {
                case '1': // newest
                    $stores->orderBy('nm_store.created_at', 'DESC');
                    break;
                case '2': // highest rating
                    $stores->orderBy('nm_store.rating_avg', 'DESC');
                    break;
                case '3': // nearest
                    $stores->orderBy('distance', 'ASC');
                    break;
                default:
                    $stores->orderBy('nm_store.created_at', 'DESC');
                    break;
            }
        }

        return $stores->paginate($data['size']);
        // return $stores->toSql();
    }

    public static function search_store($data)
    {
        $stores = Store::selectRaw(
            '*, ((((acos(sin((?*pi()/180)) * sin((`stor_latitude`*pi()/180))+cos((?*pi()/180)) * cos((`stor_latitude`*pi()/180)) * cos(((?-`stor_longitude`) * pi()/180))))*180/pi())*60*1.1515) * 1.609344) as distance', [
                $data['latitude'],
                $data['latitude'],
                $data['longitude'],
            ]
        )
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_store.stor_city')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->where('stor_type', '>', 0)
        ->where('listed', '=', 1)
        ->where('stor_status', '=', 1)
        ->where('stor_name', 'LIKE', '%'.$data['search'].'%')
        ->orderBy('distance')
        ->paginate(trim($data['size']));

        return $stores;
    }

    public static function check_rating($store_id, $cus_id)
    {
        return StoreRating::where('cus_id', '=', $cus_id)->where('store_id', '=', $store_id)->first();
    }

    public static function update_rating($rating_value, $rating_id)
    {
        $rating = StoreRating::find($rating_id);
        $rating->rating = $rating_value;
        $rating->save();

        return $rating;
    }

    public static function add_rating($data)
    {
        $rating = new StoreRating;
        $rating->store_id = trim($data['store_id']);
        $rating->cus_id = trim($data['customer_id']);
        $rating->rating = trim($data['rating']);
        $rating->save();

        return $rating;
    }

    public static function update_store_rating($store_id, $rating)
    {
        $store_rating = StoreRating::select(\DB::raw('COUNT(id) AS total, SUM(rating) AS sum'))->where('store_id', '=', $store_id)->groupBy('store_id')->first();
        $total = (!empty($store_rating)) ? $store_rating->total : 1;
        $average = (!empty($store_rating)) ? ($store_rating->sum / $store_rating->total) : $rating;

        $store = Store::find($store_id);
        $store->total_rating = $total;
        $store->rating_avg = $average;
        $store->save();

        return $store;
    }

    public static function add_review($data)
    {
        // Add Review
        $review = new StoreReview;
        $review->store_id = trim($data['store_id']);
        $review->cus_id = trim($data['customer_id']);
        $review->review = $data['review'];
        $review->status = 0;
        $review->save();

        if ($review) {
            $store_review = StoreReview::select(\DB::raw('COUNT(id) AS total'))->where('store_id', '=', trim($data['store_id']))->groupBy('store_id')->first();

            $store = Store::find(trim($data['store_id']));
            $store->total_review = $store_review->total;
            $store->save();

            return $store;
        }
    }

    public static function get_reviews($data)
    {
        $review =  StoreReview::select('nm_customer.cus_id', 'nm_customer.cus_name', 'nm_customer.cus_pic', 'store_review.created_at', 'store_review.review', 'store_review.rating')
        ->leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'store_review.cus_id')
        ->where('store_review.store_id', '=', trim($data['store_id']))
        ->where('store_review.status', '=', 1)
        ->orderBy('store_review.created_at', 'DESC')
        ->paginate(trim($data['size']));

        return $review;
    }

    public static function get_images($store_id)
    {
        return StoreImage::select('store_images.id', 'store_images.image_name', 'nm_store.stor_merchant_id')
        ->leftJoin('nm_store', 'nm_store.stor_id', '=', 'store_images.store_id')
        ->where('store_id', '=', $store_id)
        ->orderBy('store_images.id', 'asc')
        ->get();
    }

    public static function add_image($store_id, $image_name)
    {
        return StoreImage::create([
            'store_id' => $store_id,
            'image_name' => $image_name,
        ]);
    }

    public static function update_image($image_id, $image_name)
    {
        $store = StoreImage::find($image_id);
        $store->image_name = $image_name;
        $store->save();

        return $store;
    }

    public static function update_store_status($store_id,$status)
    {
        $store = Store::where('stor_id','=',$store_id)->first();

        $store->stor_status = $status;
        $store->save();

        return $store;
    }

    public static function get_store($data)
    {
        $store = Store::selectRaw(
            '*, ((((acos(sin((?*pi()/180)) * sin((`stor_latitude`*pi()/180))+cos((?*pi()/180)) * cos((`stor_latitude`*pi()/180)) * cos(((?-`stor_longitude`) * pi()/180))))*180/pi())*60*1.1515) * 1.609344) as distance', [
                $data['latitude'],
                $data['latitude'],
                $data['longitude'],
            ]
        )
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_store.stor_city')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_store.stor_merchant_id')
        ->where('stor_id', '=', trim($data['store_id']))
        ->first();

        return $store;
    }

    public static function get_store_map($data)
    {
        $store = Store::leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_store.stor_city')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_store.stor_merchant_id')
        // ->where('nm_store.stor_country', '=', $data['country_id'])
        ->where('nm_store.stor_type', '>', 0)
        ->where('nm_store.listed', '=', 1)
        ->where('nm_store.stor_status', '=', 1)
        ->whereRaw(
            'st_within(point(nm_store.stor_longitude, nm_store.stor_latitude), envelope(linestring(point(?,?), point(?,?))))', [
                trim($data['longitude_1']),
                trim($data['latitude_1']),
                trim($data['longitude_2']),
                trim($data['latitude_2']),
            ]
        );

        if ($data['search'])
            $store->where('stor_name', 'LIKE', '%'.$data['search'].'%');


        return $store->get();
    }

    public static function get_featured_offline_store($data)
    {
        $stores = Store::selectRaw(
            '*, ((((acos(sin((?*pi()/180)) * sin((`stor_latitude`*pi()/180))+cos((?*pi()/180)) * cos((`stor_latitude`*pi()/180)) * cos(((?-`stor_longitude`) * pi()/180))))*180/pi())*60*1.1515) * 1.609344) as distance', [
                $data['latitude'],
                $data['latitude'],
                $data['longitude'],
            ]
        )
        ->where('nm_store.featured', '=', 1)
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_store.stor_merchant_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->where('nm_store.stor_type', '>', 0)
        ->where('nm_store.listed', '=', 1);

        if ($data['country_code']) {
            $stores->where('nm_country.co_code', $data['country_code']);
        }

        $stores->orderBy('nm_store.updated_at', 'desc');

        return $stores->take(2)->get();
    }

    public static function get_newest_offline_store($data)
    {
        $stores = Store::selectRaw(
            '*, ((((acos(sin((?*pi()/180)) * sin((`stor_latitude`*pi()/180))+cos((?*pi()/180)) * cos((`stor_latitude`*pi()/180)) * cos(((?-`stor_longitude`) * pi()/180))))*180/pi())*60*1.1515) * 1.609344) as distance', [
                $data['latitude'],
                $data['latitude'],
                $data['longitude'],
            ]
        )
        ->where('nm_store.stor_type', '>', 0)
        ->where('nm_store.listed', '=', 1)
        ->leftJoin('nm_merchant', 'nm_merchant.mer_id', '=', 'nm_store.stor_merchant_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state');

        if ($data['country_code']) {
            $stores->where('nm_country.co_code', $data['country_code']);
        }

        return $stores->orderBy('nm_store.created_at', 'desc')->take(8)->get();
    }

    public static function api_get_stores($mer_id)
    {
        $stores = Store::leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_store.stor_city')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->where('stor_merchant_id', $mer_id )
        ->where('stor_status', 1)
        ->where('stor_type', '>', 0)
        ->get();

        $data = [];
        foreach ($stores as $key => $store) {
            $data[] = [
                'id' => $store->stor_id,
                'name' => $store->stor_name,
                'currency' => $store->co_curcode,
                'type' => ($store->stor_type == 0) ? 'Online' : 'Offline',
                'address' => $store->stor_address1 . ', ' . $store->stor_address2 . ', ' . $store->stor_zipcode . ' ' . $store->stor_city_name . ', ' . $store->name . ', ' . $store->co_name,
                'phone' => $store->stor_phone,
            ];
        }

        return $data;
    }

    public static function get_latest_reviews($store_id)
    {
       $reviews =  StoreReview::select('nm_customer.cus_id', 'nm_customer.cus_name', 'nm_customer.cus_pic', 'store_review.created_at', 'store_review.review', 'store_review.rating')
        ->leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'store_review.cus_id')
        ->where('store_review.store_id', '=', $store_id)
        ->where('store_review.status', '=', 1)
        ->orderBy('store_review.created_at', 'desc')
        ->take(2)->get();

        $data = [];
        foreach ($reviews as $key => $review) {
            $data[] = [
                'customer_id' => $review->cus_id,
                'customer_name' => $review->cus_name,
                'customer_avatar' => ($review->cus_pic) ? env('IMAGE_DIR').'/avatar/'.$review->cus_id.'/'.$review->cus_pic : '',
                'review_date' => date_format($review->created_at, 'Y-m-d H:i:s'),
                'review' => $review->review,
                'rating' => $review->rating,
            ];
        }

        return $data;
    }

    public static function add_review_rating($data)
    {
        // Add Review
        $review = new StoreReview;
        $review->store_id = trim($data['store_id']);
        $review->cus_id = trim($data['customer_id']);
        $review->review = $data['review'];
        $review->rating = $data['rating'];
        $review->status = 1;
        $review->save();

        if ($review) {
            $store_review = StoreReview::select(\DB::raw('COUNT(id) AS total'))->where('store_id', '=', $data['store_id'])->where('status', '=', 1)->first();
            $store_rating = \DB::select(\DB::raw("
                SELECT COUNT(id) as count, AVG(rating) as average FROM store_review t1
                WHERE NOT EXISTS (
                    SELECT * FROM store_review t2
                    WHERE t1.cus_id = t2.cus_id
                    AND t1.store_id = t2.store_id
                    AND t1.created_at < t2.created_at
                )
                AND store_id = ".trim($data['store_id'])."
                AND status = 1
            "));

            $store = Store::find(trim($data['store_id']));
            $store->total_review = $store_review->total;
            $store->total_rating = $store_rating[0]->count;
            $store->rating_avg = $store_rating[0]->average;
            $store->save();

            return $store;
        }
    }

    public static function update_store_listed_status($store_id)
    {
        $store = Store::where('stor_id','=',$store_id)->first();

        $store->listed = ($store->listed == 0)? 1 : 0;
        $store->save();

        return $store;
    }

    public static function get_offline_categories($store_id)
    {
        $cats = [];
        $selected_cats = StoreCategory::where('store_id', '=', $store_id)->LeftJoin('offline_categories', 'offline_categories.id', '=', 'nm_store_offline_category.offline_category_id')->get();
        foreach ($selected_cats as $key => $selected_cat) {
            $cats[$key]['details'] = $selected_cat;
            $cats[$key]['parents'] = OfflineCategory::selectRaw('GROUP_CONCAT(name_en SEPARATOR " > ") as names')->whereIn('id', explode(',', $selected_cat->parent_list))->orderBy('parent_id', 'ASC')->first();
        }

        return $cats;
    }

    public static function insert_offline_category_details($entry)
    {
        return StoreCategory::create($entry);
    }

    public static function delete_offline_category($store_id)
    {
        return StoreCategory::where('store_id', '=', $store_id)->delete();
    }

    public static function api_get_storeusers_stores($storeuser_id, $mer_id)
    {
        $stores = StoreUserMapping::where('storeuser_id', $storeuser_id)
        ->leftJoin('nm_store','nm_store.stor_id','=','nm_store_user_mappings.store_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country')
        ->leftJoin('nm_city', 'nm_city.ci_id', '=', 'nm_store.stor_city')
        ->leftJoin('nm_state', 'nm_state.id', '=', 'nm_store.stor_state')
        ->where('nm_store.stor_merchant_id', $mer_id)
        ->where('nm_store.stor_status', 1)
        ->where('stor_type', '>', 0)
        ->get();

        $data = [];
        foreach ($stores as $key => $store) {
            $data[] = [
                'id' => $store->stor_id,
                'name' => $store->stor_name,
                'currency' => $store->co_curcode,
                'type' => ($store->stor_type == 0) ? 'Online' : 'Offline',
                'address' => $store->stor_address1 . ', ' . $store->stor_address2 . ', ' . $store->stor_zipcode . ' ' . $store->stor_city_name . ', '. $store->name . ', ' . $store->co_name,
                'phone' => $store->stor_phone,
            ];
        }

        return $data;
    }

    public static function get_merchant_reviews($store_id)
    {
       $reviews =  StoreReview::select('nm_customer.cus_id', 'nm_customer.cus_name', 'store_review.created_at', 'store_review.review', 'store_review.rating')
        ->leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'store_review.cus_id')
        ->where('store_review.store_id', '=', $store_id)
        ->where('store_review.status', '=', 1)
        ->orderBy('store_review.created_at', 'desc')->paginate(50);
        return $reviews;
    }

    public static function get_store_main_category($store_id)
    {
        return StoreCategory::where('store_id', $store_id)->orderBy('id','asc')->pluck('offline_category_id')->first();
    }

    public static function get_offline_store_by_merchant_id($mer_id)
    {
        $store = Store::where('stor_merchant_id','=', $mer_id)
            ->where('stor_type','=', 1)
            ->where('stor_status', '=', 1);

        return $store->get();
    }

    public static function get_all_store($input)
    {
        $stores = Store::select('nm_store.*','nm_country.co_name','nm_merchant.mer_id','nm_merchant.mer_fname','nm_merchant.mer_lname')
        ->leftJoin('nm_merchant','nm_merchant.mer_id','=','nm_store.stor_merchant_id')
        ->leftJoin('nm_country', 'nm_country.co_id', '=', 'nm_store.stor_country');

        if($input['review'])
        $stores->where('stor_status', 2);

        return $stores->paginate(50);
    }

    public static function batch_status_update($operation, $store_id)
    {
        $update = Store::query();
        switch ($operation) {
            case 'pending_review':
                $update->where('stor_status', 2)
                ->whereIn('stor_id', $store_id)
                ->update([
                    'stor_status' => 1,
                ]);
                break;

        }

        return true;
    }

    public static function delete_gallery_ifNotIn($ids, $store_id)
    {
        return StoreImage::where('store_id', '=', $store_id)
        ->whereNotIn('id', $ids)
        ->delete();
    }

    public static function find_store($mer_id, $store_id)
    {
        return Store::where('stor_id', $store_id)->where('stor_merchant_id', $mer_id)->first();
    }

    public static function inactive_merchant_store($mer_id)
    {
        return Store::where('stor_merchant_id', $mer_id)
        ->where('stor_status', 1)->update([
            'stor_status' => 0,
        ]);
    }
}
