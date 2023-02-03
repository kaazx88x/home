<?php

namespace App\Repositories;
use App\Models\Banner;
use App\Models\BannerType;
use App\Models\BannerCountry;
use Auth;

class BannerRepo
{
    public static function get_mobile_banners($co_code = null)
    {
        $co_id = null;
        if(!empty($co_code))
            $co_id = Country::where('co_code', $co_code)->first()->value('co_id');

        $banners = Banner::where('nm_banner.type', 3);

        if($co_id)
            $banners = $banners->leftJoin('nm_banner_countries', 'nm_banner.bn_id', '=', 'nm_banner_countries.banner_id')->where('nm_banner_countries.country_id', $co_id);

        $banners = $banners->where('nm_banner.bn_status', 1)
        ->orderBy('nm_banner.order')
        ->get();

        return $banners;
    }

    public static function all_bannertypes()
    {
        $bannertypes = BannerType::all();

        foreach($bannertypes as $banner)
        {
            $banner->total = Banner::where('type',$banner->id)->count();
        }

        return $bannertypes;
    }

    public static function add_bannertype($data)
    {
        $banner = BannerType::create([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        return $banner;
    }

    public static function update_bannertype($id,$data)
    {
        $bannertype = BannerType::find($id);
        $bannertype->name = $data['name'];
        $bannertype->description = $data['description'];
        $bannertype->save();

        return $bannertype;
    }

    public static function find_bannertype($id)
    {
        return BannerType::find($id);
    }

    public static function get_banner_by_type_id($id)
    {
        return Banner::where('type','=',$id)->orderBy('order','asc')->get();
    }

    public static function add_banner($data)
    {
        $insert_to_last_order  = Banner::where('type','=',$data['type'])->count();

        $banner = Banner::create([
            'bn_title' => $data['title'],
            'bn_type' => $data['media_type'],
            'bn_img' => $data['img'],
            'bn_redirecturl' => $data['url'],
            'bn_status' => $data['status'],
            'type' => $data['type'],
            'order' => $insert_to_last_order+1,
            'bn_open' => $data['open'],
        ]);

        return $banner;
    }

    public static function find_banner($id)
    {
        return Banner::find($id);
    }

    public static function update_banner($id,$data)
    {
        $banner = Banner::find($id);
        $banner->bn_title = $data['title'];
        $banner->bn_redirecturl = $data['url'];
        $banner->bn_status = $data['status'];
        $banner->type = $data['type'];
        $banner->bn_type = $data['media_type'];
        $banner->bn_open = $data['open'];
        $banner->save();

        return $banner;
    }

    public static function delete_banner($id)
    {
        $banner = Banner::find($id);
        $type = $banner->type;
        $banner->delete();

        return $type;
    }

    public static function upload_banner_image($id,$new_file_name)
    {
        $banner = Banner::find($id);
        $banner->bn_img = $new_file_name;
        $banner->save();

        return $banner;
    }

    public static function get_sliders($co_id)
    {
        $banners = Banner::where('nm_banner.type', 1);

        if($co_id)
            $banners = $banners->leftJoin('nm_banner_countries', 'nm_banner.bn_id', '=', 'nm_banner_countries.banner_id')->where('nm_banner_countries.country_id', '=', $co_id);

        $banners = $banners->where('nm_banner.bn_status', 1)
        ->orderBy('nm_banner.order')
        ->get();

        return $banners;
    }

    public static function get_side_banners($co_id)
    {
        $banners = Banner::where('type', 2);

        if($co_id)
            $banners = $banners->leftJoin('nm_banner_countries', 'nm_banner.bn_id', '=', 'nm_banner_countries.banner_id')->where('nm_banner_countries.country_id', $co_id);

        $banners = $banners->where('nm_banner.bn_status', 1)
        ->orderBy('nm_banner.order')
        ->take(6)->get();

        return $banners;
    }

    public static function update_banner_country($id, $countries)
    {
        $banner = Banner::find($id);
        if(!$banner)
            return;

        BannerCountry::where('banner_id', $banner->bn_id)->delete();
        foreach ($countries as $co_id) {
            BannerCountry::create([
                'banner_id' => $banner->bn_id,
                'country_id' => $co_id,
            ]);
        }

        return true;
    }

    public static function remove_all_banner_country($id)
    {
        $banner = Banner::find($id);
        if(!$banner)
            return;

        BannerCountry::where('banner_id', $banner->bn_id)->delete();

        return true;
    }

}
