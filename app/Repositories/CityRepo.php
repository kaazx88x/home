<?php

namespace App\Repositories;
use DB;
use App\Models\City;


class CityRepo
{
    public static function get_cities()
    {
        $cities = City::where('ci_status', '=', 1)->get();

        return $cities;
    }

    public static function get_cities_by_country_id($ci_con_id)
    {
        $cities = City::where('ci_con_id', '=', $ci_con_id)->where('ci_status', '=', 1)->get();
        return $cities;
    }
}