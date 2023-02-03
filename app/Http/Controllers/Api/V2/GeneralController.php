<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\OfflineCategory;
use App\Models\Merchant;
use App\Models\Country;
use App\Models\ApiUser;
use App\Models\Store;
use App\Models\State;
use App\Models\Cms;
use Validator;
use Request;
use App;

class GeneralController extends Controller
{
    public function getVersion()
    {
        $input = Request::only('os', 'app', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $v = Validator::make($input, [
            'os' => 'required|alpha|max:10|in:android,ios',
            'app' => 'required|alpha|max:10|in:member,merchant',
        ]);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        try {

            $retObj = [
                'status' => 200,
            ];

            if ($input['os'] == 'android' && $input['app'] == 'merchant') {
                $app = ApiUser::where('id', 1)->first(['android_merchant_version', 'android_merchant_build_no']);
                $retObj['version'] = $app->android_merchant_version;
                $retObj['build_no'] = $app->android_merchant_build_no;
            } elseif ($input['os'] == 'android' && $input['app'] == 'member') {
                $app = ApiUser::where('id', 1)->first(['android_member_version', 'android_member_build_no']);
                $retObj['version'] = $app->android_member_version;
                $retObj['build_no'] = $app->android_member_build_no;
            } elseif ($input['os'] == 'ios' && $input['app'] == 'merchant') {
                $app = ApiUser::where('id', 1)->first(['ios_merchant_version', 'ios_merchant_build_no']);
                $retObj['version'] = $app->ios_merchant_version;
                $retObj['build_no'] = $app->ios_merchant_build_no;
            } elseif ($input['os'] == 'ios' && $input['app'] == 'member') {
                $app = ApiUser::where('id', 1)->first(['ios_member_version', 'ios_member_build_no']);
                $retObj['version'] = $app->ios_member_version;
                $retObj['build_no'] = $app->ios_member_build_no;
            } else {
                return abort(422);
            }

            $retObj['download_url'] = url('cms', [7]);

            return \Response::json($retObj);

        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
            ], 500);
        }
    }

    public function getGeneralRates()
    {
        $input = Request::only('merchant_id', 'lang','store_id');
        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $mer_id = null;
        if (isset($input['merchant_id'])) {
            $v = Validator::make($input, [
                'merchant_id' => 'integer',
            ]);

            $mer_id = trim($input['merchant_id']);
            if ($v->fails())
                $mer_id = null;
        }

        $store_id = null;
        if (isset($input['store_id'])) {
            $v = Validator::make($input, [
                'store_id' => 'integer',
            ]);

            $store_id = trim($input['store_id']);
            if ($v->fails())
                $store_id = null;
        }

        try {
            return \Response::json([
                'status' => 200,
                'exchange_rates' => $this->getCurrencyExchangeRates($mer_id, $store_id),
                'customer_service_charge_rate' => $this->getCustomerServiceCharge($mer_id),
                'platform_charge_rate' => $this->getPlatformCharge($mer_id),
            ]);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
            ], 500);
        }
    }

    public function getCurrency()
    {
        $countries = Country::where('co_offline_status', 1)->get();
        $currencies = [];
        foreach ($countries as $country) {
            $currencies[$country->co_curcode] = (double)$country->co_offline_rate;
        }

        return \Response::json($currencies);
    }

    public function getCountries()
    {
        $countries = Country::where('co_offline_status', 1)->get();
        $country_result = [];
        foreach ($countries as $country) {
            $country_result[] = [
                'id' => $country->co_id,
                'name' => $country->co_name,
                'code' => $country->co_code,
                'flag' => url('web/lib/flag-icon/flags/4x3/') . '/' . strtolower($country->co_code) . '.png',
                'phone_area_code' => $country->phone_country_code,
            ];
        }

        return \Response::json([
            'status' => 200,
            'message' => 'Retrieve country success',
            'data' => $country_result
        ]);
    }

    public function getCategories()
    {
        $cats = OfflineCategory::where('status','=',1)->orderBy('sequence', 'ASC')->get();
        $category = array(
            'items' => array(),
            'parents' => array()
        );

        foreach ($cats as $cat) {
            $category['items'][$cat->id] = $cat;
            $category['parents'][$cat->parent_id][] = $cat->id;
        }

        return \Response::json([
            'status' => 200,
            'message' => 'Retrieve categories success',
            'data' => self::build_category_menu(0, $category)
        ]);
    }

    public function getLocation()
    {
        $input = Request::only('latitude', 'longitude');

        if (isset($input['latitude']) && isset($input['longitude'])) {
            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $input['latitude'] . "," . $input['longitude'] . "&key=" . env('GMAP_APIKEY') . "&result_type=country";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = json_decode(curl_exec($ch));
            curl_close($ch);

            $countrycode = '';
            if (isset($result->results[0]->address_components[0]->short_name)) {
                $countrycode = $result->results[0]->address_components[0]->short_name;
            }

            $country = Country::where('co_offline_status', 1)->where('co_code', $countrycode)->first();
            if (!$country) {
                $country = Country::where('co_offline_status', 1)->where('co_code', 'MY')->first();
            }

            return \Response::json([
                'status' => 200,
                'message' => 'Retrieve country location success',
                'data' => [
                    'id' => $country->co_id,
                    'name' => $country->co_name,
                    'code' => $country->co_code,
                    'flag' => url('web/lib/flag-icon/flags/4x3/') . '/' . strtolower($country->co_code) . '.png',
                ]
            ]);
        } else {
            $ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
            // $ip = "210.245.20.170";
            $iplong = ip2long($ip);
            $sql = 'SELECT  c.iso_code_2 as countrycode, responded_country_id  FROM  ip2nationcountries c, ip2nation i  WHERE  i.ip < INET_ATON("' . $ip . '")  AND  c.code = i.country ORDER BY i.ip DESC LIMIT 0,1';
            $ipcountry = \DB::select($sql);

            $countryid = 5;
            if (!empty($ipcountry))
            {
                if ($ipcountry[0]->responded_country_id != 0) {
                    $countryid = $ipcountry[0]->responded_country_id;
                }
            }

            $country = Country::where('co_offline_status', 1)->where('co_id', $countryid)->first();
            if (!$country) {
                $country = Country::where('co_offline_status', 1)->where('co_id', 5)->first();
            }

            return \Response::json([
                'status' => 200,
                'message' => 'Retrieve country location success',
                'data' => [
                    'id' => $country->co_id,
                    'name' => $country->co_name,
                    'code' => $country->co_code,
                    'flag' => url('web/lib/flag-icon/flags/4x3/') . '/' . strtolower($country->co_code) . '.png',
                ]
            ]);
        }
    }

    private function getCurrencyExchangeRates($mer_id=null, $store_id=null)
    {
        $stores_country = Store::where('stor_merchant_id',$mer_id);

        if ($store_id)
            $stores_country = $stores_country->where('stor_id',$store_id);

        $stores_country = $stores_country->pluck('stor_country')->toArray();

        if($stores_country)
        {
            $countries = Country::where('co_offline_status', 1)
                                ->wherein('co_id',$stores_country)
                                ->select('co_curcode', 'co_offline_rate')
                                ->orderBy('co_id','DESC')
                                ->get();
        }
        else
        {
            $countries = Country::where('co_offline_status', 1)
                                ->select('co_curcode', 'co_offline_rate')
                                ->orderBy('co_id','DESC')
                                ->get();
        }

        $currencies = [];
        foreach ($countries as $country) {
            $currencies[$country->co_curcode] = (double)$country->co_offline_rate;
        }

        return $currencies;
    }

    private function getCustomerServiceCharge($mer_id = null)
    {
        $merchant = Merchant::find($mer_id);

        return ($merchant) ? (double)$merchant->mer_service_charge : (double)\Config::get('settings.offline_service_charge');
    }

    private function getPlatformCharge($mer_id = null)
    {
        $merchant = Merchant::find($mer_id);

        return ($merchant) ? (double)$merchant->mer_platform_charge : (double)\Config::get('settings.offline_platform_charge');
    }

    private static function build_category_menu($parent, $menu) {
        $category = [];
        if (isset($menu['parents'][$parent])) {
            foreach ($menu['parents'][$parent] as $key => $itemId) {
                    $category[$key]['id'] = $menu['items'][$itemId]->id;
                    $category[$key]['name'] = $menu['items'][$itemId]->name;
                    $category[$key]['image'] = ($menu['items'][$itemId]->image) ? env('IMAGE_DIR').'/offline-category/image/'.$menu['items'][$itemId]->image : '';
                    $category[$key]['banner'] = ($menu['items'][$itemId]->banner) ? env('IMAGE_DIR').'/offline-category/banner/'.$menu['items'][$itemId]->banner : '';
                    $category[$key]['childs'] = (isset($menu['parents'][$itemId])) ? self::build_category_menu($itemId, $menu) : [];
            }
        }

        return $category;
    }

    // public function goBeta()
    // {
    //     return \Response::json([
    //         'goto' => 'beta'
    //         // 'goto' => null
    //     ]);
    // }

    public function getStates()
    {
        $input = Request::only('country_id');

        $v = Validator::make($input, [
            'country_id' => 'required|integer'
        ]);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        try {
            $states = State::where('country_id', $input['country_id'])->where('status', 1)->get();

            $result = [];
            foreach ($states as $state) {
                $result[] = [
                    'id' => $state->id,
                    'name' => $state->name
                ];
            }

            return \Response::json([
                'status' => 200,
                'message' => 'Retrieve state success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
            ], 500);
        }
    }

    public function getFaq()
    {
        $input = Request::only('type', 'lang');
        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $v = Validator::make($input, [
            'type' => 'required'
        ]);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        switch ($input['type']) {
            case 'ecard':
                $type = 2;
                break;

            default:
                $type = 0;
        }

        try {
            $faqs = Cms::where('cp_cms_type', $type)->where('cp_status', 1)->get();

            $result = [];
            foreach ($faqs as $faq) {
                $result[] = [
                    'id' => $faq->cp_id,
                    'title' => $faq->title,
                    'description' => $faq->description,
                ];
            }

            return \Response::json([
                'status' => 200,
                'message' => 'Retrieve faq success',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
            ], 500);
        }
    }
}