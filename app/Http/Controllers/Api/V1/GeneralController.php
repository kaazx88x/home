<?php

namespace App\Http\Controllers\Api\V1;

use App;
use App\Http\Controllers\Controller;
use Validator;
use App\Models\Country;
use App\Models\ApiUser;
use App\Models\Merchant;
use App\Models\OfflineCategory;
use Request;
use App\Models\Store;

class GeneralController extends Controller {
    public function getVersion()
    {
        $input = Request::only('os', 'app', 'lang');

        if (isset($input['lang']))
            App::setLocale($input['lang']);

        // data validation
        $v = Validator::make($input, [
            'os' => 'required',
            'app' => 'required',
        ]);

        if ($v->fails()) {
            $intErrors = $v->messages()->messages();
            $errors['status'] = 0;
            $errors['message'] = reset($intErrors)[0];

            return $errors;
        }

        try {

            $retObj = [
                'status' => 1,
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
                'status' => 0,
                'message' => trans('api.systemError')
            ]);
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

    // Not needed as mobile app will perform the conversion on the fly
    // public function postExchange()
    // {
    //     $data = \Request::only('originCurrency', 'amount');
    //     $amount = (double)$data['amount'];
    //     $originCountry = Country::where(['co_curcode' => $data['originCurrency'], 'co_offline_status' => 1])->first();
    //     if (isset($originCountry)) {
    //         $data['status'] = 'success';
    //         $data['vCoin'] = ceil($amount / $originCountry->co_rate);
    //         return \Response::json($data);
    //     }

    //     return \Response::json([
    //         'status' => 'fail',
    //         'message' => 'conversion fail',
    //     ], 200);
    // }

    public function getGeneralRates()
    {
        $input = Request::only('lang', 'merchant_id','store_id');
        $lang = $input['lang'];
        if (isset($lang))
            App::setLocale($lang);

       
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
                'status' => 1,
                'exchange_rates' => $this->getCurrencyExchangeRates($mer_id, $store_id),
                'customer_service_charge_rate' => $this->getCustomerServiceCharge($mer_id),
                'platform_charge_rate' => $this->getPlatformCharge($mer_id),
            ]);
        } catch (\Exception $e) {
            return \Response::json([
                'status' => 0,
                'message' => trans('api.systemError')
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

    public function getCountries()
    {
        $countries = Country::where('co_offline_status', 1)->get();
        $country_result = [];
        foreach ($countries as $country) {
            $country_result[] = [
                'name' => $country->co_name,
                'code' => $country->co_code,
                'flag' => url('web/lib/flag-icon/flags/4x3/') . '/' . strtolower($country->co_code) . '.png',
            ];
        }

        return \Response::json([
            'status' => 1,
            'message' => 'Retrieve country success',
            'data' => $country_result
        ]);
    }

    public function getCategories()
    {
        $input = Request::only('lang');
        $lang = $input['lang'];
        if (isset($lang))
            App::setLocale($lang);

        $parents = OfflineCategory::where('parent_id','=', 0)->where('status','=', 1)->orderBy('sequence','ASC')->get();

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
            'status' => 1,
            'message' => 'Retrieve categories success',
            'data' => self::build_category_menu(0, $category)
        ]);
    }

    private static function build_category_menu($parent, $menu) {
        $category = [];
        if (isset($menu['parents'][$parent])) {
            foreach ($menu['parents'][$parent] as $key => $itemId) {
                    $category[$key]['id'] = $menu['items'][$itemId]->id;
                    $category[$key]['name'] = $menu['items'][$itemId]->name;
                    $category[$key]['image'] = ($menu['items'][$itemId]->image) ? asset('images/offline_category').'/'.$menu['items'][$itemId]->image : null;
                    $category[$key]['childs'] = (isset($menu['parents'][$itemId])) ? self::build_category_menu($itemId, $menu) : [];
            }
        }

        return $category;
    }

    public function getVersionBeta()
    {
        $input = Request::only('version');

        if ($input['version'] == 10)
            return \Response::json([
                'goto' => 'Beta'
            ]);

        return \Response::json([
            'goto' => 'Live'
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
                'status' => 1,
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
                'status' => 1,
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
}