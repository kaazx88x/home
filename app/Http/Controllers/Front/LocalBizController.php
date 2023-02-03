<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Front\Controller;
use Request;

class LocalBizController extends Controller
{
    public function details($store_id)
    {
        // $input = Request::only('store_id');
        // $store_id = trim($input['store_id']);

        $app_link = 'meihome://local-biz/details?store_id=' . $store_id;

        return view('front.localbiz.details', compact('app_link'));
    }
}