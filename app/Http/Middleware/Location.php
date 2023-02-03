<?php

namespace App\Http\Middleware;

use Closure;
use Session;
use DB;
use Config;
use Cookie;

class Location
{

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->is('api/*'))
        {
            $this->getClientCountryCode();
        }
        return $next($request);
    }

    private function getClientCountryCode()
    {

        $countryid = "";

        if (Cookie::get('country_locale') != null)
            $countryid = Cookie::get('country_locale');
        elseif (Session::has('countryid'))
            $countryid = Session::get('countryid');

        if (empty($countryid))
        {
            $ip = isset($_SERVER['HTTP_CF_CONNECTING_IP']) ? $_SERVER['HTTP_CF_CONNECTING_IP'] : $_SERVER['REMOTE_ADDR'];
            // $ip = '210.245.20.170';

            $iplong = ip2long($ip);
            $sql = 'SELECT  c.iso_code_2 as countrycode, responded_country_id  FROM  ip2nationcountries c, ip2nation i  WHERE  i.ip < INET_ATON("' . $ip . '")  AND  c.code = i.country ORDER BY i.ip DESC LIMIT 0,1';
            $ipcountry = DB::select($sql);

            $countryid = 5;
            if (!empty($ipcountry))
            {
                if ($ipcountry[0]->responded_country_id != 0) {
                    $countryid = $ipcountry[0]->responded_country_id;
                }
            }
        }

        $timezone = DB::select('SELECT timezone FROM nm_country WHERE co_id = '. $countryid);

        $tz = 'Asia/Kuala_Lumpur';
        if (!empty($timezone)) {
            if ($timezone[0]->timezone) {
                $tz = $timezone[0]->timezone;
            }
        }

        session(['countryid' => $countryid]);
        session(['timezone' => $tz]);
        Cookie::queue(Cookie::forever('country_locale', $countryid));
        Cookie::queue(Cookie::forever('timezone', $tz));
    }

}
