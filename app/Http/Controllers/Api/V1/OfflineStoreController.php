<?php

namespace App\Http\Controllers\Api\V1;

use Validator;
use App\Http\Controllers\Controller;
use App\Repositories\StoreRepo;
use App\Repositories\CustomerRepo;
use App\Repositories\BannerRepo;

class OfflineStoreController extends Controller
{

    public function __construct(StoreRepo $storerepo, CustomerRepo $customerrepo, BannerRepo $bannerrepo)
    {
        $this->store = $storerepo;
        $this->customer = $customerrepo;
        $this->banner = $bannerrepo;
    }

    public function getStoreListing()
    {
        $data = \Request::only('longitude', 'latitude', 'page', 'size', 'lang', 'featured', 'search', 'range', 'country_code', 'category_id', 'sort');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'longitude' => trans('api.longitude'),
            'latitude' => trans('api.latitude'),
            'page' => trans('api.page'),
            'size' => trans('api.size'),
            'featured' => trans('api.featured'),
            'newest' => trans('api.newest'),
            'search' => trans('api.search'),
            'range' => trans('api.range'),
        );

        $v = Validator::make($data, [
            'longitude' => 'required',
            'latitude' => 'required',
            'page' => 'required|integer',
            'size' => 'required|integer',
            'featured' => 'required|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {
            $stores = $this->store->get_store_listing($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.failRetrieve')
            ]);
        }

        $details = [];
        foreach ($stores as $key => $store) {
            $store_images = $this->store->get_images($store->stor_id);
            $reviews = $this->store->get_latest_reviews($store->stor_id);
            $images = [];
            foreach ($store_images as $key => $sm) {
                $link = env('IMAGE_DIR').'/store/'.$sm->stor_merchant_id.'/'.$sm->image_name;
                array_push($images, $link);
            }

            $categories = $this->store->get_offline_categories($store->stor_id);
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
            'status' => 1,
            'message' => trans('api.successRetrieve'),
            'count' => $stores->total(),
            'total_pages' => $stores->lastPage(),
            'data' => $details,
        ]);
    }

    public function searchStore()
    {
        $data = \Request::only('search', 'longitude', 'latitude', 'page', 'size', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'search' => trans('api.search'),
            'longitude' => trans('api.longitude'),
            'latitude' => trans('api.latitude'),
            'page' => trans('api.page'),
            'size' => trans('api.size'),
        );

        $v = Validator::make($data, [
            // 'search' => 'required',
            'longitude' => 'required',
            'latitude' => 'required',
            'page' => 'required|integer',
            'size' => 'required|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {
            $stores = $this->store->search_store($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.failRetrieve')
            ]);
        }
        // return $stores;
        $details = [];
        foreach ($stores as $key => $store) {
            $store_images = $this->store->get_images($store->stor_id);
            $images = [];
            foreach ($store_images as $key => $sm) {
                $link = env('IMAGE_DIR').'/store/'.$sm->stor_merchant_id.'/'.$sm->image_name;
                array_push($images, $link);
            }

            $categories = $this->store->get_offline_categories($store->stor_id);
            $store_categories = [];
            foreach ($categories as $key => $category) {
                $store_categories[] = $category['details']->name_en;
            }

            $details[] = [
                'store_id' => $store->stor_id,
                'merchant_id' => $store->stor_merchant_id,
                'store_name' => $store->stor_name,
                'store_phone' => $store->stor_phone,
                'store_address' => $store->stor_address1.', '.$store->stor_address2.', '.$store->stor_zipcode.' '.$store->stor_city_name.', '.(!empty($store-> stor_state) ? $store->name : $store->ci_name).', '.$store->co_name,
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
                'store_categories' => $store_categories,
                'distance' => $store->distance,
            ];
        }

        return \Response::json([
            'status' => 1,
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

        // data validation
        $niceNames = array(
            'store_id' => trans('api.store_id'),
        );

        $v = Validator::make($data, [
            'store_id' => 'required|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        // store validation
        $store = $this->store->get_store_by_id(trim($data['store_id']));
        if (empty($store))
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store_id') . trans('api.notFound'),
            ]);

        // get review list
        try {
            $images = $this->store->get_images(trim($data['store_id']));
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.failRetrieve'),
            ]);
        }

        $details = [];
        foreach ($images as $key => $image) {
            $link = env('IMAGE_DIR').'/store/'.$image->stor_merchant_id.'/'.$image->image_name;
            array_push($details, $link);
        }

        return \Response::json([
            'status' => 1,
            'message' => trans('api.successRetrieve'),
            'data' => $details,
        ]);
    }

    public function getReviews()
    {
        $data = \Request::only('store_id', 'page', 'size', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'store_id' => trans('api.store_id'),
            'page' => trans('api.page'),
            'size' => trans('api.size'),
        );

        $v = Validator::make($data, [
            'store_id' => 'required|integer',
            'page' => 'required|integer',
            'size' => 'required|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        // store validation
        $store = $this->store->get_store_by_id(trim($data['store_id']));
        if (empty($store))
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store_id') . trans('api.notFound'),
            ]);

        // get review list
        try {
            $reviews = $this->store->get_reviews($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.failRetrieve'),
            ]);
        }

        $details = [];
        foreach ($reviews as $key => $review) {
            $details[] = [
                'customer_id' => $review->cus_id,
                'customer_name' => $review->cus_name,
                'review_date' => date_format($review->created_at, 'Y-m-d H:i:s'),
                'review' => $review->review,
                'rating' => $review->rating,
            ];
        }

        return \Response::json([
            'status' => 1,
            'message' => trans('api.successRetrieve'),
            'count' => $reviews->total(),
            'total_pages' => $reviews->lastPage(),
            'data' => $details,
        ]);
    }

    public function addReview()
    {
        $data = \Request::only('store_id', 'customer_id', 'review', 'lang', 'rating');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'store_id' => trans('api.store_id'),
            'customer_id' => trans('api.customer_id'),
            'review' => trans('api.review'),
            'rating' => trans('api.rating'),
        );

        $v = Validator::make($data, [
            'store_id' => 'required|integer',
            'customer_id' => 'required|integer',
            // 'review' => 'required',
            'rating' => 'required|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        // store validation
        $store = $this->store->get_store_by_id(trim($data['store_id']));
        if (empty($store))
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store_id') . trans('api.notFound'),
            ]);

        // customer validation
        $customer = $this->customer->get_customer_by_id(trim($data['customer_id']));
        if (empty($customer))
            return \Response::json([
                'status' => 0,
                'message' => trans('api.customer_id') . trans('api.notFound'),
            ]);

        // Add review
        try {
            if (!isset($data['review']))
                $data['review'] = '';

            $review = $this->store->add_review_rating($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.addReview') . trans('api.fail'),
            ]);
        }

        return \Response::json([
            'status' => 1,
            'message' => trans('api.addReview') . trans('api.success'),
        ]);
    }

    public function addRating()
    {
        $data = \Request::only('store_id', 'customer_id', 'rating', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'store_id' => trans('api.store_id'),
            'customer_id' => trans('api.customer_id'),
            'rating' => trans('api.rating'),
        );

        $v = Validator::make($data, [
            'store_id' => 'required|integer',
            'customer_id' => 'required|integer',
            'rating' => 'required|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        // store validation
        $store = $this->store->get_store_by_id(trim($data['store_id']));
        if (empty($store))
            return \Response::json([
                'status' => 0,
                'message' => trans('api.store_id') . trans('api.notFound'),
            ]);

        // customer validation
        $customer = $this->customer->get_customer_by_id(trim($data['customer_id']));
        if (empty($customer))
            return \Response::json([
                'status' => 0,
                'message' => trans('api.customer_id') . trans('api.notFound'),
            ]);

        // check exist : store_rating
        $checkrating = $this->store->check_rating(trim($data['store_id']), trim($data['customer_id']));

        if ($checkrating) {
            try {
                $rating = $this->store->update_rating($data['rating'], $checkrating->id);
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.addRating') . trans('api.fail'),
                ]);
            }
        } else {
            try {
                $rating = $this->store->add_rating($data);
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.addRating') . trans('api.fail'),
                ]);
            }
        }

        if ($rating) {
            try {
                $store = $this->store->update_store_rating($rating->store_id, $rating->rating);
            } catch (\Exception $e) {
                return \Response::json([
                    'status' => 0,
                    'message' => trans('api.addRating') . trans('api.fail'),
                ]);
            }
        }

        return \Response::json([
            'status' => 1,
           'message' => trans('api.addRating') . trans('api.success'),
            'rating_total' => $store->total_rating,
            'rating_average' => $store->rating_avg
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
        $niceNames = array(
            'store_id' => trans('api.store_id'),
            'longitude' => trans('api.longitude'),
            'latitude' => trans('api.latitude'),
        );

        // data validation
        $v = Validator::make($data, [
            'store_id' => 'required|integer',
            'longitude' => 'required',
            'latitude' => 'required',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {
            $store = $this->store->get_store($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.failRetrieve'),
            ]);
        }

        if (!$store) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.failRetrieve'),
            ]);
        }

        $store_images = $this->store->get_images($store->stor_id);
        $images = [];
        foreach ($store_images as $key => $sm) {
            $link = env('IMAGE_DIR').'/store/'.$sm->stor_merchant_id.'/'.$sm->image_name;
            array_push($images, $link);
        }

        $categories = $this->store->get_offline_categories($store->stor_id);
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
            'store_categories' => $store_categories,
            'distance' => $store->distance,
        ];

        return \Response::json([
            'status' => 1,
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

        // data validation
        $niceNames = array(
            'latitude_1' => trans('api.latitude') . ' 1',
            'longitude_1' => trans('api.longitude') . ' 1',
            'latitude_2' => trans('api.latitude') . ' 2',
            'longitude_2' => trans('api.longitude') . ' 2',
            'search' => trans('api.search'),
            // 'country_id' => trans('api.country_id'),
        );

        // data validation
        $v = Validator::make($data, [
            'latitude_1' => 'required',
            'longitude_1' => 'required',
            'latitude_2' => 'required',
            'longitude_2' => 'required',
            // 'country_id' => 'required|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {
            $stores = $this->store->get_store_map($data);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.failRetrieve'),
            ]);
        }

        $details = [];
        foreach ($stores as $key => $store) {
            $store_images = $this->store->get_images($store->stor_id);
            $reviews = $this->store->get_latest_reviews($store->stor_id);
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
            'status' => 1,
            'message' => trans('api.successRetrieve'),
            'data' => $details,
        ]);
    }

    public function getStoreRange()
    {
        $data = \Request::only('latitude', 'longitude', 'range', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        // data validation
        $niceNames = array(
            'latitude_1' => trans('api.latitude') . ' 1',
            'longitude_1' => trans('api.longitude') . ' 1',
            'range' => trans('api.range'),
            // 'country_id' => trans('api.country_id'),
        );

        // data validation
        $v = Validator::make($data, [
            'latitude' => 'required',
            'longitude' => 'required',
            'range' => 'required',
            // 'country_id' => 'required|integer',
        ]);
        $v->setAttributeNames($niceNames);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {
            $pdo = \DB::connection()->getPdo();
            $pdo->setAttribute(\PDO::ATTR_EMULATE_PREPARES, true);
            $stmt = $pdo->prepare('CALL store_in_range(?,?,?)');
            $stmt->bindParam(1, $data['latitude']);
            $stmt->bindParam(2, $data['longitude']);
            $stmt->bindParam(3, $data['range']);
            $stmt->execute();
            $stores = $stmt->fetchAll();
            $stmt->closeCursor();
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.failRetrieve'),
            ]);
        }

        $details = [];
        foreach ($stores as $key => $store) {
            $store_images = $this->store->get_images($store['stor_id']);
            $images = [];
            foreach ($store_images as $key => $sm) {
                $link = env('IMAGE_DIR').'/store/'.$sm['stor_merchant_id'].'/'.$sm['image_name'];
                array_push($images, $link);
            }

            $details[] = [
                'store_id' => $store['stor_id'],
                'merchant_id' => $store['stor_merchant_id'],
                'store_name' => $store['stor_name'],
                'store_phone' => $store['stor_phone'],
                'store_address' => $store['stor_address1'].', '.$store['stor_address2'].', '.$store['stor_zipcode'].' '.$store['stor_city_name'].', '.(!empty($store['stor_state']) ? $store['name'] : $store['ci_name']).', '.$store['co_name'],
                'store_website' => $store['stor_website'],
                'store_main_image' => env('IMAGE_DIR').'/store/'.$store['stor_merchant_id'].'/'.$store['stor_img'],
                'store_image' => $images,
                'store_type' => $store['stor_type'],
                'store_latitude' => $store['stor_latitude'],
                'store_longitude' => $store['stor_longitude'],
            ];
        }

        return \Response::json([
            'status' => 1,
            'message' => trans('api.successRetrieve'),
            'data' => $details,
        ]);
    }

    public function getHome()
    {
        $data = \Request::only('country_code', 'latitude', 'longitude', 'lang');

        if (isset($data['lang'])) {
            \App::setLocale($data['lang']);
        }
        unset($data['lang']);

        $data['latitude'] = (isset($data['latitude'])) ? $data['latitude'] : 0;
        $data['longitude'] = (isset($data['longitude'])) ? $data['longitude'] : 0;

        # Get Banner for Mobile
        $banners = $this->banner->get_mobile_banners();
        $banner_data = [];

        if ($banners) {
            foreach ($banners as $key => $banner) {
                // $image = asset('images/banner').'/'.$banner['bn_img'];
                // if ($banner->bn_type == 2)
                //     $link = $banner['bn_img'];

                // array_push($banner_data, $link);

                $image = env('IMAGE_DIR') . '/banner/' . $banner['bn_img'];
                $banner_data[] = [
                    'type' => $banner->bn_type,
                    'image' => $image,
                    'link' => $banner->bn_redirecturl
                ];
            }
        }

        # Get 2 featured store
        $featured = $this->store->get_featured_offline_store($data);
        $featured_data = [];

        if ($featured) {
            foreach ($featured as $key => $feat) {
                $feat_store_images = $this->store->get_images($feat->stor_id);
                $feat_reviews = $this->store->get_latest_reviews($feat->stor_id);
                $feat_images = [];
                foreach ($feat_store_images as $key => $fsm) {
                    $link = env('IMAGE_DIR').'/store/'.$fsm->stor_merchant_id.'/'.$fsm->image_name;
                    array_push($feat_images, $link);
                }

                $categories = $this->store->get_offline_categories($feat->stor_id);
                $store_categories = [];
                foreach ($categories as $key => $category) {
                    $store_categories[] = $category['details']->name_en;
                }

                $featured_data[] = [
                    'store_id' => $feat->stor_id,
                    'merchant_id' => $feat->stor_merchant_id,
                    'merchant_email' => $feat->email,
                    'store_name' => $feat->stor_name,
                    'store_phone' => $feat->stor_phone,
                    'store_address_1' => $feat->stor_address1,
                    'store_address_2' => $feat->stor_address2,
                    'store_zipcode' => $feat->stor_zipcode,
                    'store_city' => $feat->stor_city_name,
                    'store_state' => (!empty($feat->stor_state)) ? $feat->name : $feat->ci_name,
                    'store_country' => $feat->co_name,
                    'store_address' => $feat->stor_address1.', '.$feat->stor_address2.', '.$feat->stor_zipcode.' '.$feat->stor_city_name.', '.((!empty($feat->stor_state)) ? $feat->name : $feat->ci_name).', '.$feat->co_name,
                    'store_website' => $feat->stor_website,
                    'store_main_image' => env('IMAGE_DIR').'/store/'.$feat->stor_merchant_id.'/'.$feat->stor_img,
                    'store_image' => $feat_images,
                    'store_type' => $feat->stor_type,
                    'featured' => $feat->featured,
                    'accept_payment' => $feat->accept_payment,
                    'store_office_hour' => $feat->office_hour,
                    'store_rating_average' => $feat->rating_avg,
                    'store_total_rating' => $feat->total_rating,
                    'store_total_review' => $feat->total_review,
                    'store_latitude' => $feat->stor_latitude,
                    'store_longitude' => $feat->stor_longitude,
                    'store_short_description' => $feat->short_description,
                    'store_long_description' => $feat->long_description,
                    'store_reviews' => $feat_reviews,
                    'store_categories' => $store_categories,
                    'distance' => $feat->distance,
                ];
            }
        }

        # Get 8 newest store
        $newest = $this->store->get_newest_offline_store($data);
        $newest_data = [];

        if ($newest) {
            foreach ($newest as $key => $new) {
                $new_store_images = $this->store->get_images($new->stor_id);
                $new_reviews = $this->store->get_latest_reviews($new->stor_id);
                $new_images = [];
                foreach ($new_store_images as $key => $nsm) {
                    $link = env('IMAGE_DIR').'/store/'.$nsm->stor_merchant_id.'/'.$nsm->image_name;
                    array_push($new_images, $link);
                }

                $categories = $this->store->get_offline_categories($new->stor_id);
                $store_categories = [];
                foreach ($categories as $key => $category) {
                    $store_categories[] = $category['details']->name_en;
                }

                $newest_data[] = [
                    'store_id' => $new->stor_id,
                    'merchant_id' => $new->stor_merchant_id,
                    'merchant_email' => $new->email,
                    'store_name' => $new->stor_name,
                    'store_phone' => $new->stor_phone,
                    'store_address_1' => $new->stor_address1,
                    'store_address_2' => $new->stor_address2,
                    'store_zipcode' => $new->stor_zipcode,
                    'store_city' => $new->stor_city_name,
                    'store_state' => (!empty($new->stor_state)) ? $new->name : $new->ci_name,
                    'store_country' => $new->co_name,
                    'store_address' => $new->stor_address1.', '.$new->stor_address2.', '.$new->stor_zipcode.' '.$new->stor_city_name.', '.((!empty($new->stor_state)) ? $new->name : $new->ci_name).', '.$new->co_name,
                    'store_website' => $new->stor_website,
                    'store_main_image' => env('IMAGE_DIR').'/store/'.$new->stor_merchant_id.'/'.$new->stor_img,
                    'store_image' => $new_images,
                    'store_type' => $new->stor_type,
                    'featured' => $new->featured,
                    'accept_payment' => $new->accept_payment,
                    'store_office_hour' => $new->office_hour,
                    'store_rating_average' => $new->rating_avg,
                    'store_total_rating' => $new->total_rating,
                    'store_total_review' => $new->total_review,
                    'store_latitude' => $new->stor_latitude,
                    'store_longitude' => $new->stor_longitude,
                    'store_short_description' => $new->short_description,
                    'store_long_description' => $new->long_description,
                    'store_reviews' => $new_reviews,
                    'store_categories' => $store_categories,
                    'distance' => $new->distance,
                ];
            }
        }

        return \Response::json([
            'status' => 1,
            'data' => [
                'banner' => $banner_data,
                'featured' => $featured_data,
                'newest' => $newest_data
            ]
        ]);
    }

}