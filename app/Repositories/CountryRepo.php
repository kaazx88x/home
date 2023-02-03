<?php

namespace App\Repositories;
use DB;
use App\Models\Country;


class CountryRepo
{
    public static function get_countries($country_list_array = array())
    {
        return Country::where('co_status', '=', 1)
            ->whereIn('co_id',$country_list_array)
            ->orderBy('co_name')
            ->get();
    }

    public static function get_all_countries()
    {
        return Country::where('co_status', '=', 1)
            ->orderBy('co_name')
            ->get();
    }

    public static function get_country_name_by_id($co_id)
    {
        $name = Country::where('co_id', '=', $co_id)->pluck('co_name')->first();

        return $name;
    }

    public static function get_country_name_by_code($co_code)
    {
        $name = Country::where('co_code', '=', $co_code)->pluck('co_name')->first();

        return $name;
    }

    public static function get_country_by_code($co_code)
    {
        $name = Country::where('co_code', '=', $co_code)->first();

        return $name;
    }

    public static function all()
    {
        return Country::all();
    }

    public static function add($data)
    {
        $country = Country::create([
            'co_name' => $data['name'],
            'co_code' => $data['code'],
            'co_cursymbol' => $data['cursymbol'],
            'co_curcode' => $data['curcode'],
            'co_rate' => $data['rate'],
            'co_status' => $data['status'],
            'co_offline_rate' => $data['offline_rate'],
            'co_offline_status' => $data['offline_status'],
        ]);
    }

    public static function find($id)
    {
        return Country::find($id);
    }

    public static function update($id,$data)
    {
        $country = Country::find($id);
        $country->co_name = $data['name'];
        $country->co_code = $data['code'];
        $country->co_cursymbol = $data['cursymbol'];
        $country->co_curcode = $data['curcode'];
        $country->co_rate = $data['rate'];
        $country->co_status = $data['status'];
        $country->co_offline_rate = $data['offline_rate'];
        $country->co_offline_status = $data['offline_status'];
        $country->save();

        return $country;
    }

    public static function delete($id)
    {
        $country = Country::find($id);
        $country->delete();

        return $country;
    }

    public static function get_countries_where_not_in($country_id)
    {
        return Country::where('co_status', '=', 1)->whereNotIn('co_id', $country_id)->get();
    }

    public static function get_country_by_locale()
    {
        return Country::where('co_id', '=', session('countryid'))->first();
    }

    public static function get_offline_ountries($admin_country_id_list = array())
    {
        return Country::where('co_status', 1)
            ->whereIn('co_id',$admin_country_id_list)
            ->where('co_offline_status', 1)
            ->orderBy('co_name')
            ->get();
    }

    public static function get_all_offline_ountries()
    {
        return Country::where('co_offline_status', 1)
            ->orderBy('co_name')
            ->get();
    }

    public static function get_country_timezone_by_id($co_id)
    {
        return Country::where('co_id', '=', $co_id)->pluck('timezone')->first();
    }

    public static function get_country_by_id($co_id)
    {
        return Country::where('co_id', '=', $co_id)->first();
    }

    public static function get_countries_id_name()
    {
        return Country::where('co_status', '=', 1)
                ->select('co_id as id','co_name as name', 'phone_country_code as phone_areacode')
                ->orderBy('co_name')
                ->get();
    }
}