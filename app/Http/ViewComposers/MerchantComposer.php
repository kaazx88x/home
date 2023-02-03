<?php

namespace App\Http\ViewComposers;

use Auth;
use Illuminate\View\View;
use App\Repositories\MerchantRepo;
use App\Repositories\StoreRepo;

class MerchantComposer
{
    /**
     * Create a movie composer.
     *
     * @return void
     */
    public function __construct(MerchantRepo $merchantrepo, StoreRepo $storerepo)
    {
        $this->merchant = $merchantrepo;
        $this->store = $storerepo;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        if(\Auth::guard('merchants')->check())
        {
            $mer_id = (\Auth::guard('merchants'))? \Auth::guard('merchants')->user()->mer_id : 0;
            $merchant = $this->merchant->get_merchant($mer_id);
            $store_count = count($this->store->get_stores_by_merchant($mer_id));
            $type = 'merchants';
            $route = 'merchant';
            $view->with('merchant', $merchant)->with('store_count', $store_count)->with('logintype', $type)->with('route',$route);

        }

        if(\Auth::guard('storeusers')->check()) {
            $mer_id = (\Auth::guard('storeusers'))? \Auth::guard('storeusers')->user()->mer_id : 0;
            $merchant = $this->merchant->get_merchant($mer_id);
            $type = 'storeusers';
            $name = \Auth::guard('storeusers')->user()->name;
            $route = 'store';
            $view->with('merchant', $merchant)->with('logintype', $type)->with('name',$name)->with('route',$route);
        }
    }
}
