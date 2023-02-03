<?php
namespace App\Http\Controllers\Front;
use Illuminate\Support\Facades\Input as Input;
use App\Http\Controllers\Front\Controller;
use App\Repositories\ProductRepo;
use App\Repositories\OrderRepo;
use App\Repositories\AttributeRepo;
use App\Repositories\FilterRepo;
use App\Repositories\CategoryRepo;
use App\Models\Country;
use Cookie;
use Auth;

class ProductController extends Controller
{
    public function all()
    {
        $filterParams = (\Request::get('options')) ? explode('-', base64_decode(\Request::get('options'))) : '';
        $rangeParams = (\Request::get('range')) ? explode('-', \Request::get('range')) : '';
        $itemParams = (\Request::get('items')) ? \Request::get('items') : '15';
        $sortParams = (\Request::get('sort')) ? \Request::get('sort') : 'new';

        $products = ProductRepo::all_active($filterParams, $rangeParams, $itemParams, $sortParams, null);
        $filters_array = FilterRepo::get_category_filter(0);
        $products->appends(Input::except('page'));
        $category = trans('localize.all_products');
        $co_cursymbol = Country::where('co_id','=',\Cookie::get('country_locale'))->value('co_cursymbol');
        return view('front.product.list', compact('products','filterParams','rangeParams', 'itemParams', 'sortParams','co_cursymbol','filters_array', 'category'));
    }

    // Original Version of searching by category ID instead of url_slug.
    /*public function category($id)
    {
        $filterParams = (\Request::get('options')) ? explode('-', base64_decode(\Request::get('options'))) : '';
        $rangeParams = (\Request::get('range')) ? explode('-', \Request::get('range')) : '';
        $itemParams = (\Request::get('items')) ? \Request::get('items') : '15';
        $sortParams = (\Request::get('sort')) ? \Request::get('sort') : 'new';
        
        $catid = base64_decode($id);

        if(is_numeric($catid)){
            $category = CategoryRepo::get_category_by_id($catid)->name;    
        }else{
            $category = CategoryRepo::get_category_by_slug($id)->name;
        }
        
        $products = ProductRepo::all_by_category($catid, $filterParams, $rangeParams, $itemParams, $sortParams, null);
        $products->appends(Input::except('page'));
        $filters_array = FilterRepo::get_category_filter($catid);
        $co_cursymbol = Country::where('co_id','=',\Cookie::get('country_locale'))->value('co_cursymbol');
        return view('front.product.list', compact('products', 'filterParams', 'rangeParams', 'itemParams', 'sortParams','co_cursymbol','filters_array', 'category'));
    }*/

    public function category($parent, $child = null)
    {
        $filterParams = (\Request::get('options')) ? explode('-', base64_decode(\Request::get('options'))) : '';
        $rangeParams = (\Request::get('range')) ? explode('-', \Request::get('range')) : '';
        $itemParams = (\Request::get('items')) ? \Request::get('items') : '15';
        $sortParams = (\Request::get('sort')) ? \Request::get('sort') : 'new';
        
        // Purpose of decoding due to foreseen the situation where url_slug has not been set,
        // it will take the encoded ID from the original version of searching which is
        // decoding the encoded ID from frontend.

        $parent_id = base64_decode($parent);

        if(is_numeric($parent_id)){
            $category = CategoryRepo::get_category_by_id($parent_id)->name;    
        }else{
            $parent_id = CategoryRepo::get_category_by_slug($parent)->id;
            $category = CategoryRepo::get_category_by_slug($parent)->name;
        }

        if($child){
            $child_id = base64_decode($child);

            if(is_numeric($child_id)){
                $category = CategoryRepo::get_category_by_id($child_id)->name; 
            }else{
                $child_id = CategoryRepo::get_category_by_slug($child)->id;
                $category = CategoryRepo::get_category_by_slug($child)->name;
            }
        }
        
        $products = ProductRepo::all_by_category(($child)?$child_id:$parent_id, $filterParams, $rangeParams, $itemParams, $sortParams, null);
        $products->appends(Input::except('page'));
        $filters_array = FilterRepo::get_category_filter(($child)?$child_id:$parent_id);
        $co_cursymbol = Country::where('co_id','=',\Cookie::get('country_locale'))->value('co_cursymbol');
        return view('front.product.list', compact('products', 'filterParams', 'rangeParams', 'itemParams', 'sortParams','co_cursymbol','filters_array', 'category'));
    }

    public function detail($id)
    {
        $platform_fees = round(\Config::get('settings.platform_charge'));
        $product = ProductRepo::detail($id);

        $countryid = \Cookie::get('country_locale')==null?\Session::get('countryid'):\Cookie::get('country_locale');
        $attribute_listing = AttributeRepo::get_product_attribute_listing_by_pro_id($id, $countryid);
        if (!$product || !$product['pricing'])
            return redirect('/products');

        $product['pricing']->price = round($product['pricing']->price, 2);

        if (Cookie::get('cart_token') == null) {
            $cart_token = md5(uniqid(microtime()));
            Cookie::queue(Cookie::forever('cart_token', $cart_token));
        } else {
            $cart_token = Cookie::get('cart_token');
        }

        $user_id = (Auth::user()) ? Auth::user()->cus_id : null;
        $tot_in_cart = OrderRepo::get_quantity_of_product($cart_token, $user_id, $id);

        return view('front.product.detail', ['product' => $product,'total_in_cart' => $tot_in_cart, 'attribute_listing' => $attribute_listing]);
    }
}
