<?php

namespace App\Http\ViewComposers;

use Cookie;
use Session;
use Auth;
use Illuminate\View\View;
use App\Models\Country;
use App\Repositories\CountryRepo;

class CountryComposer
{
    /**
     * Create a movie composer.
     *
     * @return void
     */
    public function __construct(CountryRepo $countryRepo)
    {
        $this->country = $countryRepo;

    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $countries = $this->country->get_all_countries();
        $country_name = 'Country Selection';
        $country_timezone = 'Timezone Selection';
        $countryid = "";

        if (Cookie::get('country_locale') != null)
            $countryid = Cookie::get('country_locale');
        elseif (Session::has('countryid'))
            $countryid = Session::get('countryid');

        if ($countryid)
        {
            $country_name = $this->country->get_country_name_by_id($countryid);
            $country_timezone = $this->country->get_country_timezone_by_id($countryid);
        }
        else
        {
            $countryid = Cookie::get('countryid');
            if ($countryid) {
                $country = Country::where('co_id', $countryid)->get();
                $country_name = $country->co_name;
                $country_timezone = $country->timezone;

                session(['countryid' => $country->co_id]);
                session(['timezone' => $country->timezone]);
                Cookie::queue(Cookie::forever('country_locale', $country->co_id));
                Cookie::queue(Cookie::forever('timezone', $country->timezone));
            }
        }

        $view->with('locale_countries', $countries)->with('country_name', $country_name)->with('country_timezone', $country_timezone);
    }
}