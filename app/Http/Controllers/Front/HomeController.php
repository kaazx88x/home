<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Front\Controller;
use Illuminate\Support\Facades\Session;
use App\Repositories\ProductRepo;
use Cookie;
use Validator;
use Illuminate\Mail\Mailer;
use Illuminate\Mail\Message;
use App\Repositories\HomeRepo;
use App\Repositories\CountryRepo;
use Illuminate\Http\Request;
use App\Repositories\ProductImageRepo;
use App\Models\Country;

class HomeController extends Controller
{
    public function __construct(ProductRepo $productrepo, Mailer $mailer, HomeRepo $homerepo, ProductImageRepo $productimagerepo)
    {
        $this->product = $productrepo;
        $this->mailer = $mailer;
        $this->home = $homerepo;
        $this->image = $productimagerepo;
    }

    public function about_us()
    {
        return view('front.home.about_us');
    }

    /*
        Disabled for DM Mall (Now only offline mode, no online mode)
    */
    // public function index()
    // {
    //     $platform_fees = number_format(\Config::get('settings.platform_charge'));
    //     $featured = $this->product->featured_product();

    //     foreach ($featured as $key => $feat) {
    //         foreach ($feat['products'] as $key => $product) {
    //             $product->is_discounted = 0;
    //             if ( $product->discounted_price != 0.00) {
    //                 $today = new \DateTime;
    //                 $discounted_from = new \DateTime($product->discounted_from);
    //                 $discounted_to = new \DateTime($product->discounted_to);
    //                 if (($today >= $discounted_from) && ($today <= $discounted_to))
    //                 {
    //                     $product->is_discounted = 1;
    //                     // $product->discounted_price = number_format($product->discounted_price + ($product->discounted_price / $platform_fees),2);
    //                     $product->discounted_price = number_format($product->discounted_price,2);
    //                 }

    //             }

    //             $product->price = number_format($product->price, 2);
    //         }
    //     }

    //     $country_code = trim(\Request::input('country'));
    //     $country = Country::where('co_code', $country_code)->where('co_status', 1)->first();
    //     $reload = false;
    //     if ($country && Cookie::get('country_locale') === null) {
    //         Cookie::queue(Cookie::forever('country_locale', $country->co_id));
    //         $reload = true;
    //     }

    //     return view('front.home.index', ['featured' => $featured, 'featured_count' => count($featured), 'reload' => $reload]);
    // }

    public function index()
    {
        $featured = $this->product->featured_product();

        foreach ($featured as $category_id => $data) {
            if($data['products']->isEmpty()) {
                unset($featured[$category_id]);
            }
        }

        if(empty($featured))
            return redirect('/profile');

        $country_code = trim(\Request::input('country'));
        $country = Country::where('co_code', $country_code)->where('co_status', 1)->first();
        $reload = false;
        if ($country && session('countryid') === null) {
            // Cookie::queue(Cookie::forever('country_locale', $country->co_id));
            session(['countryid' => $country->co_id]);
            $reload = true;
        }

        return view('front.home.index', ['featured' => $featured, 'featured_count' => count($featured), 'reload' => $reload]);
    }

    public function setlocale()
    {
        $data = \Request::all();
        Session::forget('lang');
        Session::put('lang', $data['lang']);
        return 'success';
    }

    public function setcountry()
    {
        $data = \Request::all();
        $country = CountryRepo::get_country_by_id($data['id']);

        session(['countryid' => $country->co_id]);
        session(['timezone' => $country->timezone]);
        Cookie::queue(Cookie::forever('country_locale', $country->co_id));
        Cookie::queue(Cookie::forever('timezone', $country->timezone));
        Cookie::queue(Cookie::forever('countryid', $data['id']));
        return 'success';
    }

    public function contactUs()
    {
    	if(\Request::isMethod('post'))
        {
            $data = \Request::all();

            $v = Validator::make($data, [
	            'subject' => 'required',
	            'email' => 'required|email',
                'message' => 'required|max:255'
	            ]);

            if ($v->fails())
               return back()->withInput()->withErrors($v);

            $save = $this->home->insert_contactUs($data);
            if ($save == true)
            {
                $this->mailer->send('front.emails.contact_us', array('details'=>$data), function (Message $m) use ($data) {
                    $m->to($data['email'])->subject(config('app.name').' Inquiries');
                });

                return view('front.home.contact_us', ['success'=>'Your inquiries has been send, we will respond to your inquiries immediately']);
            }
            return view('front.home.contact_us');
        }
        return view('front.home.contact_us');
    }

    public function cms($url_slug)
    {
        $cms = $this->home->find_cms($url_slug);
        if ($cms) {
            return view('front.home.cms', ['cms'=>$cms]);
        }

        return view('errors.404');
    }

    public function search()
    {
        $category = (\Request::get('category')) ? \Request::get('category') : 'all';
        $search = (\Request::get('search')) ? \Request::get('search') : '';
        $itemParams = (\Request::get('items')) ? \Request::get('items') : '15';
        $sortParams = (\Request::get('sort')) ? \Request::get('sort') : 'new';
        $results = $this->home->search_product($category, $search, $itemParams, $sortParams);

        foreach ($results as $key => $result) {
            $result->main_image = $this->image->get_product_main_image($result->pro_id);

            $result->price = number_format($result->price,2);
            $result->is_discounted = 0;
            if ( $result->discounted_price > 0.00) {
                $today = new \DateTime;
                $discounted_from = new \DateTime($result->discounted_from);
                $discounted_to = new \DateTime($result->discounted_to);
                if (($today >= $discounted_from) && ($today <= $discounted_to))
                {
                    $result->is_discounted = 1;
                    $result->discounted_price = number_format($result->discounted_price,2);
                }
            }
        }

        return view('front.home.search', compact('results', 'category', 'search', 'sortParams', 'itemParams'));
    }
}
