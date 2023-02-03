<?php

namespace App\Http\Controllers\Api\V2\member;

use Validator;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\OfflineCategory;
use App\Repositories\StoreRepo;
use App\Repositories\CustomerRepo;
use App\Repositories\BannerRepo;

class StoreController extends Controller
{
    protected $niceNames;
    // protected $customer;
    protected $cus_id;

    public function __construct()
    {
        if (\Auth::guard('api_members')->check()) {
            $this->cus_id = \Auth::guard('api_members')->user()->cus_id;
            // $this->customer = Customer::find(trim($this->cus_id));
        }

        $this->niceNames = [
            'store_id' => trans('api.store_id'),
            'longitude' => trans('api.longitude'),
            'latitude' => trans('api.latitude'),
            'page' => trans('api.page'),
            'size' => trans('api.size'),
            'featured' => trans('api.featured'),
            'newest' => trans('api.newest'),
            'search' => trans('api.search'),
            'range' => trans('api.range'),
            'customer_id' => trans('api.customer_id'),
            'review' => trans('api.review'),
            'rating' => trans('api.rating'),
            'latitude_1' => trans('api.latitude') . ' 1',
            'longitude_1' => trans('api.longitude') . ' 1',
            'latitude_2' => trans('api.latitude') . ' 2',
            'longitude_2' => trans('api.longitude') . ' 2',
            'country_id' => trans('api.country_id'),
        ];
    }

    public function getStoreListing()
    {
        $data = \Request::only('longitude', 'latitude', 'page', 'size', 'lang', 'featured', 'search', 'range', 'country_code', 'category_id', 'sort');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $v = Validator::make($data, [
            'longitude' => 'required',
            'latitude' => 'required',
            'page' => 'required|integer',
            'size' => 'required|integer',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        // try {
            $stores = StoreRepo::get_store_listing($data);
        // } catch (\Exception $e) {
        //     return \Response::json([
        //         'status' => 500,
        //         'message' => trans('api.failRetrieve')
        //     ], 500);
        // }

        $details = [];
        foreach ($stores as $key => $store) {
            $store_images = StoreRepo::get_images($store->stor_id);
            $reviews = StoreRepo::get_latest_reviews($store->stor_id);
            $images = [];
            foreach ($store_images as $key => $sm) {
                $link = env('IMAGE_DIR').'/store/'.$sm->stor_merchant_id.'/'.$sm->image_name;
                array_push($images, $link);
            }

            $categories = StoreRepo::get_offline_categories($store->stor_id);
            $store_categories = [];
            foreach ($categories as $key => $category) {
                $store_categories[] = $category['details']->name_en;
            }

            $details[] = [
                'store_id' => $store->stor_id,
                'merchant_id' => $store->stor_merchant_id,
                'merchant_email' => $store->email,
                'store_name' => $store->stor_name,
                'store_phone' => $store->stor_phone,
                'stor_office_number' => $store->stor_office_number,
                'store_address_1' => $store->stor_address1,
                'store_address_2' => $store->stor_address2,
                'store_zipcode' => $store->stor_zipcode,
                'store_city' => $store->stor_city_name,
                'store_state' => (!empty($store->stor_state)) ? $store->name : $store->ci_name,
                'store_country' => $store->co_name,
                'store_address' => $store->stor_address1.', '.$store->stor_address2.', '.$store->stor_zipcode.' '.$store->stor_city_name.', '.((!empty($store->stor_state)) ? $store->name : $store->ci_name).', '.$store->co_name,
                'store_website' => $store->stor_website,
                'store_main_image' => env('IMAGE_DIR').'/store/'.$store->stor_merchant_id.'/'.$store->stor_img,
                'store_image' => $images,
                'store_type' => $store->stor_type,
                'featured' => $store->featured,
                'accept_payment' => $store->accept_payment,
                'store_office_hour' => $store->office_hour,
                'store_rating_average' => $store->rating_avg,
                'store_total_rating' => $store->total_rating,
                'store_total_review' => $store->total_review,
                'store_latitude' => $store->stor_latitude,
                'store_longitude' => $store->stor_longitude,
                'store_short_description' => $store->short_description,
                'store_long_description' => $store->long_description,
                'store_reviews' => $reviews,
                'store_categories' => $store_categories,
                'distance' => $store->distance,
            ];
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.successRetrieve'),
            'count' => $stores->total(),
            'total_pages' => $stores->lastPage(),
            'data' => $details,
        ]);
    }

    public function getImages()
    {
        $data = \Request::only('store_id', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $v = Validator::make($data, [
            'store_id' => 'required|integer',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        // store validation
        $store = StoreRepo::get_store_by_id(trim($data['store_id']));
        if (empty($store))
            return \Response::json([
                'status' => 404,
                'message' => trans('api.store_id') . trans('api.notFound'),
            ], 404);

        // get review list
        try {
            $images = StoreRepo::get_images(trim($data['store_id']));
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.failRetrieve'),
            ], 500);
        }

        $details = [];
        foreach ($images as $key => $image) {
            $link = env('IMAGE_DIR').'/store/'.$image->stor_merchant_id.'/'.$image->image_name;
            array_push($details, $link);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.successRetrieve'),
            'data' => $details,
        ], 200);
    }

    public function getReviews()
    {
        $data = \Request::only('store_id', 'page', 'size', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $v = Validator::make($data, [
            'store_id' => 'required|integer',
            'page' => 'required|integer',
            'size' => 'required|integer',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        // store validation
        $store = StoreRepo::get_store_by_id(trim($data['store_id']));
        if (empty($store))
            return \Response::json([
                'status' => 404,
                'message' => trans('api.store_id') . trans('api.notFound'),
            ], 404);

        // get review list
        try {
            $reviews = StoreRepo::get_reviews($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.failRetrieve'),
            ], 500);
        }

        $details = [];
        foreach ($reviews as $key => $review) {
            $details[] = [
                'customer_id' => $review->cus_id,
                'customer_name' => $review->cus_name,
                'customer_avatar' => ($review->cus_pic) ? env('IMAGE_DIR') . '/avatar/' . $review->cus_id . '/' . $review->cus_pic : '',
                'review_date' => date_format($review->created_at, 'Y-m-d H:i:s'),
                'review' => $review->review,
                'rating' => $review->rating,
            ];
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.successRetrieve'),
            'count' => $reviews->total(),
            'total_pages' => $reviews->lastPage(),
            'data' => $details,
        ]);
    }

    public function addReview()
    {
        $data = \Request::only('store_id', 'review', 'lang', 'rating');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $v = Validator::make($data, [
            'store_id' => 'required|integer',
            // 'review' => 'required',
            'rating' => 'required|integer',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        // store validation
        $store = StoreRepo::get_store_by_id(trim($data['store_id']));
        if (empty($store))
            return \Response::json([
                'status' => 404,
                'message' => trans('api.store_id') . trans('api.notFound'),
            ], 404);

        // customer validation
        $customer = CustomerRepo::get_customer_by_id($this->cus_id);
        if (empty($customer))
            return \Response::json([
                'status' => 404,
                'message' => trans('api.memberId') . trans('api.notFound'),
            ], 404);

        // Add review
        try {
            $data['customer_id'] = $this->cus_id;
            $review = StoreRepo::add_review_rating($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.addReview') . trans('api.fail'),
            ], 500);
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.addReview') . trans('api.success'),
        ]);
    }

    public function getStore()
    {
        $data = \Request::only('store_id', 'longitude', 'latitude', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $v = Validator::make($data, [
            'store_id' => 'required|integer',
            'longitude' => 'required',
            'latitude' => 'required',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        try {
            $store = StoreRepo::get_store($data);

            if (!$store) {
                return \Response::json([
                    'status' => 500,
                    'message' => trans('api.failRetrieve'),
                ], 500);
            }

        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.failRetrieve'),
            ], 500);
        }

        $store_images = StoreRepo::get_images($store->stor_id);
        $reviews = StoreRepo::get_latest_reviews($store->stor_id);
        $images = [];
        foreach ($store_images as $key => $sm) {
            $link = env('IMAGE_DIR').'/store/'.$sm->stor_merchant_id.'/'.$sm->image_name;
            array_push($images, $link);
        }

        $categories = StoreRepo::get_offline_categories($store->stor_id);
        $store_categories = [];
        foreach ($categories as $key => $category) {
            $store_categories[] = $category['details']->name_en;
        }

        $details = [
            'store_id' => $store->stor_id,
            'merchant_id' => $store->stor_merchant_id,
            'merchant_email' => $store->email,
            'store_name' => $store->stor_name,
            'store_phone' => $store->stor_phone,
            'stor_office_number' => $store->stor_office_number,
            'store_address_1' => $store->stor_address1,
            'store_address_2' => $store->stor_address2,
            'store_zipcode' => $store->stor_zipcode,
            'store_city' => $store->stor_city_name,
            'store_state' => (!empty($store->stor_state)) ? $store->name : $store->ci_name,
            'store_country' => $store->co_name,
            'store_address' => $store->stor_address1.', '.$store->stor_address2.', '.$store->stor_zipcode.' '.$store->stor_city_name.', '.((!empty($store->stor_state)) ? $store->name : $store->ci_name).', '.$store->co_name,
            'store_website' => $store->stor_website,
            'store_image' => $images,
            'store_type' => $store->stor_type,
            'store_office_hour' => $store->office_hour,
            'featured' => $store->featured,
            'accept_payment' => $store->accept_payment,
            'store_main_image' => env('IMAGE_DIR').'/store/'.$store->stor_merchant_id.'/'.$store->stor_img,
            'store_rating_average' => $store->rating_avg,
            'store_total_rating' => $store->total_rating,
            'store_total_review' => $store->total_review,
            'store_latitude' => $store->stor_latitude,
            'store_longitude' => $store->stor_longitude,
            'store_short_description' => $store->short_description,
            'store_long_description' => $store->long_description,
            'store_reviews' => $reviews,
            'store_categories' => $store_categories,
            'distance' => $store->distance,
        ];

        return \Response::json([
            'status' => 200,
            'message' => trans('api.successRetrieve'),
            'data' => $details,
        ]);
    }

    public function getStoreMap()
    {
        $data = \Request::only('latitude_1', 'longitude_1', 'latitude_2', 'longitude_2', 'lang', 'search');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $v = Validator::make($data, [
            'latitude_1' => 'required',
            'longitude_1' => 'required',
            'latitude_2' => 'required',
            'longitude_2' => 'required',
            // 'country_id' => 'required|integer',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        try {
            $stores = StoreRepo::get_store_map($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.failRetrieve'),
            ], 500);
        }

        $details = [];
        foreach ($stores as $key => $store) {
            $store_images = StoreRepo::get_images($store->stor_id);
            $reviews = StoreRepo::get_latest_reviews($store->stor_id);
            $images = [];
            foreach ($store_images as $key => $sm) {
                $link = env('IMAGE_DIR').'/store/'.$sm->stor_merchant_id.'/'.$sm->image_name;
                array_push($images, $link);
            }

            $details[] = [
                'store_id' => $store->stor_id,
                'merchant_id' => $store->stor_merchant_id,
                'merchant_email' => $store->email,
                'store_name' => $store->stor_name,
                'store_phone' => $store->stor_phone,
                'stor_office_number' => $store->stor_office_number,
                'store_address_1' => $store->stor_address1,
                'store_address_2' => $store->stor_address2,
                'store_zipcode' => $store->stor_zipcode,
                'store_city' => $store->stor_city_name,
                'store_state' => (!empty($store->stor_state)) ? $store->name : $store->ci_name,
                'store_country' => $store->co_name,
                'store_address' => $store->stor_address1.', '.$store->stor_address2.', '.$store->stor_zipcode.' '.$store->stor_city_name.', '.((!empty($store->stor_state)) ? $store->name : $store->ci_name).', '.$store->co_name,
                'store_website' => $store->stor_website,
                'store_main_image' => env('IMAGE_DIR').'/store/'.$store->stor_merchant_id.'/'.$store->stor_img,
                'store_image' => $images,
                'store_type' => $store->stor_type,
                'featured' => $store->featured,
                'accept_payment' => $store->accept_payment,
                'store_office_hour' => $store->office_hour,
                'store_rating_average' => $store->rating_avg,
                'store_total_rating' => $store->total_rating,
                'store_total_review' => $store->total_review,
                'store_latitude' => $store->stor_latitude,
                'store_longitude' => $store->stor_longitude,
                'store_short_description' => $store->short_description,
                'store_long_description' => $store->long_description,
                'store_reviews' => $reviews,
            ];
        }

        return \Response::json([
            'status' => 200,
            'message' => trans('api.successRetrieve'),
            'data' => $details,
        ]);
    }

    // public function getHome()
    // {
    //     $data = \Request::only('country_code', 'latitude', 'longitude', 'lang');

    //     if (isset($data['lang'])) {
    //         \App::setLocale($data['lang']);
    //     }
    //     unset($data['lang']);

    //     $data['latitude'] = (isset($data['latitude'])) ? $data['latitude'] : 0;
    //     $data['longitude'] = (isset($data['longitude'])) ? $data['longitude'] : 0;

    //     # Get Banner for Mobile
    //     $banners = BannerRepo::get_mobile_banners();
    //     $banner_data = [];

    //     if ($banners) {
    //         foreach ($banners as $key => $banner) {
    //             // $link = asset('images/banner').'/'.$banner['bn_img'];
    //             // array_push($banner_data, $link);

    //             $image = env('IMAGE_DIR') . '/banner/' . $banner['bn_img'];
    //             $banner_data[] = [
    //                 'id' => $banner->bn_id,
    //                 'type' => $banner->bn_type,
    //                 'image' => $image,
    //                 'link' => $banner->bn_redirecturl,
    //                 'popup' => $banner->bn_popup
    //             ];
    //         }
    //     }

    //     # Get 2 featured store
    //     $featured = StoreRepo::get_featured_offline_store($data);
    //     $featured_data = [];

    //     if ($featured) {
    //         foreach ($featured as $key => $feat) {
    //             $feat_store_images = StoreRepo::get_images($feat->stor_id);
    //             $feat_reviews = StoreRepo::get_latest_reviews($feat->stor_id);
    //             $feat_images = [];
    //             foreach ($feat_store_images as $key => $fsm) {
    //                 $link = env('IMAGE_DIR').'/store/'.$fsm->stor_merchant_id.'/'.$fsm->image_name;
    //                 array_push($feat_images, $link);
    //             }

    //             $categories = StoreRepo::get_offline_categories($feat->stor_id);
    //             $store_categories = [];
    //             foreach ($categories as $key => $category) {
    //                 $store_categories[] = $category['details']->name_en;
    //             }

    //             $featured_data[] = [
    //                 'store_id' => $feat->stor_id,
    //                 'merchant_id' => $feat->stor_merchant_id,
    //                 'merchant_email' => $feat->email,
    //                 'store_name' => $feat->stor_name,
    //                 'store_phone' => $feat->stor_phone,
    //                 'store_address_1' => $feat->stor_address1,
    //                 'store_address_2' => $feat->stor_address2,
    //                 'store_zipcode' => $feat->stor_zipcode,
    //                 'store_city' => $feat->stor_city_name,
    //                 'store_state' => (!empty($feat->stor_state)) ? $feat->name : $feat->ci_name,
    //                 'store_country' => $feat->co_name,
    //                 'store_address' => $feat->stor_address1.', '.$feat->stor_address2.', '.$feat->stor_zipcode.' '.$feat->stor_city_name.', '.((!empty($feat->stor_state)) ? $feat->name : $feat->ci_name).', '.$feat->co_name,
    //                 'store_website' => $feat->stor_website,
    //                 'store_main_image' => env('IMAGE_DIR').'/store/'.$feat->stor_merchant_id.'/'.$feat->stor_img,
    //                 'store_image' => $feat_images,
    //                 'store_type' => $feat->stor_type,
    //                 'featured' => $feat->featured,
    //                 'accept_payment' => $feat->accept_payment,
    //                 'store_office_hour' => $feat->office_hour,
    //                 'store_rating_average' => $feat->rating_avg,
    //                 'store_total_rating' => $feat->total_rating,
    //                 'store_total_review' => $feat->total_review,
    //                 'store_latitude' => $feat->stor_latitude,
    //                 'store_longitude' => $feat->stor_longitude,
    //                 'store_short_description' => $feat->short_description,
    //                 'store_long_description' => $feat->long_description,
    //                 'store_reviews' => $feat_reviews,
    //                 'store_categories' => $store_categories,
    //                 'distance' => $feat->distance,
    //             ];
    //         }
    //     }

    //     # Get 8 newest store
    //     $newest = StoreRepo::get_newest_offline_store($data);
    //     $newest_data = [];

    //     if ($newest) {
    //         foreach ($newest as $key => $new) {
    //             $new_store_images = StoreRepo::get_images($new->stor_id);
    //             $new_reviews = StoreRepo::get_latest_reviews($new->stor_id);
    //             $new_images = [];
    //             foreach ($new_store_images as $key => $nsm) {
    //                 $link = env('IMAGE_DIR').'/store/'.$nsm->stor_merchant_id.'/'.$nsm->image_name;
    //                 array_push($new_images, $link);
    //             }

    //             $categories = StoreRepo::get_offline_categories($new->stor_id);
    //             $store_categories = [];
    //             foreach ($categories as $key => $category) {
    //                 $store_categories[] = $category['details']->name_en;
    //             }

    //             $newest_data[] = [
    //                 'store_id' => $new->stor_id,
    //                 'merchant_id' => $new->stor_merchant_id,
    //                 'merchant_email' => $new->email,
    //                 'store_name' => $new->stor_name,
    //                 'store_phone' => $new->stor_phone,
    //                 'store_address_1' => $new->stor_address1,
    //                 'store_address_2' => $new->stor_address2,
    //                 'store_zipcode' => $new->stor_zipcode,
    //                 'store_city' => $new->stor_city_name,
    //                 'store_state' => (!empty($new->stor_state)) ? $new->name : $new->ci_name,
    //                 'store_country' => $new->co_name,
    //                 'store_address' => $new->stor_address1.', '.$new->stor_address2.', '.$new->stor_zipcode.' '.$new->stor_city_name.', '.((!empty($new->stor_state)) ? $new->name : $new->ci_name).', '.$new->co_name,
    //                 'store_website' => $new->stor_website,
    //                 'store_main_image' => env('IMAGE_DIR').'/store/'.$new->stor_merchant_id.'/'.$new->stor_img,
    //                 'store_image' => $new_images,
    //                 'store_type' => $new->stor_type,
    //                 'featured' => $new->featured,
    //                 'accept_payment' => $new->accept_payment,
    //                 'store_office_hour' => $new->office_hour,
    //                 'store_rating_average' => $new->rating_avg,
    //                 'store_total_rating' => $new->total_rating,
    //                 'store_total_review' => $new->total_review,
    //                 'store_latitude' => $new->stor_latitude,
    //                 'store_longitude' => $new->stor_longitude,
    //                 'store_short_description' => $new->short_description,
    //                 'store_long_description' => $new->long_description,
    //                 'store_reviews' => $new_reviews,
    //                 'store_categories' => $store_categories,
    //                 'distance' => $new->distance,
    //             ];
    //         }
    //     }

    //     return \Response::json([
    //         'status' => 200,
    //         'data' => [
    //             'banner' => $banner_data,
    //             'featured' => $featured_data,
    //             'newest' => $newest_data
    //         ]
    //     ]);
    // }

    public function getHome()
    {
        $data = \Request::only('lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $featcats = OfflineCategory::where('status', 1)->where('featured', 1)->orderBy('sequence', 'ASC')->get();

        $featured_categories = [];

        foreach ($featcats as $key => $cat) {
            $featured_categories[] = [
                'id' => $cat->id,
                'name' => $cat->name,
                'image' => ($cat->image) ? env('IMAGE_DIR').'/offline-category/image/'.$cat->image : '',
                'banner' => ($cat->banner) ? env('IMAGE_DIR').'/offline-category/banner/'.$cat->banner : ''
            ];
        }

        return \Response::json([
            'status' => 200,
            'data' => [
                'featured_categories' => $featured_categories
            ]
        ]);
    }
}