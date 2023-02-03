<?php

namespace App\Http\ViewComposers;

use Cookie;
use Auth;
use Illuminate\View\View;
use App\Repositories\BannerRepo;

class BannerComposer
{
    public function __construct()
    {
        # code...
    }

    public function compose(View $view)
    {
        $co_id = \Cookie::get('country_locale')? \Cookie::get('country_locale') : null;
        $sliders = BannerRepo::get_sliders($co_id);
        $sides = BannerRepo::get_side_banners($co_id);

        $view->with('sliders', $sliders)->with('sides', $sides);
    }
}