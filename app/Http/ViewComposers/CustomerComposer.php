<?php

namespace App\Http\ViewComposers;

use Cookie;
use Auth;
use Illuminate\View\View;
use App\Repositories\CustomerRepo;

class CustomerComposer
{
    /**
     * Create a movie composer.
     *
     * @return void
     */
    public function __construct(CustomerRepo $customerRepo)
    {
        $this->customer = $customerRepo;

    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        $cus_id = (Auth::user()) ? Auth::user()->cus_id : 0;
        $vcoin = $this->customer->getCustomerVCoinBalance($cus_id);
        $paymentcode = $this->customer->getCustomerPaymentSecureCode($cus_id);
        $cus_details = $this->customer->get_customer_by_id($cus_id);
        $cus_wallet = $this->customer->get_customer_available_wallet($cus_id);

        $view->with('cus_vc', $vcoin)->with('cu_code', $paymentcode)->with('cus_details', $cus_details)->with('wallets', $cus_wallet);
    }
}
