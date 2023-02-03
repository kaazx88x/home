<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Front\Controller;
use Illuminate\Support\Facades\Session;
use App\Repositories\CustomerRepo;
use Validator;
use Illuminate\Http\Request;
use App\Models\LuckyDraw;
use App\Models\ApiUser;
use App\Models\VcoinLog;
use App\Models\GamePointLog;

class LuckyDrawController extends Controller
{

    public function __construct(CustomerRepo $customerrepo)
    {
        $this->customer = $customerrepo;
    }

    public function luckyDraw($reward_code, $cus_id)
    {
        $api_token = \Request::get('api_token');
        //check validation and exsisting of api user from api token
        $api_user = ApiUser::where('id', '=', 1)->where('api_token', '=', $api_token)->first();

        if (!$api_user)
            return view('front.luckydraw.invalid');


        //check if api user is exsist in customer table
        $customer = $this->customer->get_customer_by_id($cus_id);
        if (!$customer)
            return view('front.luckydraw.invalid');
      
       
        //check either lucky draw reward code is exsist and not claimed yet
        $luckydraw = LuckyDraw::where('reward_code', '=', $reward_code)->whereNull('claim_by_cus_id')->first();
        if ($luckydraw)
        {
            $dt = new \DateTime();
            $dt->format('Y-m-d H:i:s');
            $luckydraw->claim_by_cus_id = $cus_id;
            $luckydraw->claim_date = $dt;
            $luckydraw->save();

            $rewardtype = $luckydraw->reward_type;
            $rewardvalue = $luckydraw->reward_value;
            $rewarddescription = $luckydraw->reward_description;

            $type = strtolower($rewardtype);
            switch ($type)
            {
                case "v":
                    $old_vtoken = $customer->v_token;
                    $new_vtoken = $old_vtoken + $rewardvalue;
                    $customer->v_token = $new_vtoken;
                    $customer->save();

                    $vcoinlog = new VcoinLog;
                    //update vcoin log
                    $vcoinlog->cus_id = $cus_id;
                    $vcoinlog->Credit_amount = $rewardvalue;
                    $vcoinlog->remark = 'Lucky Draw Winner';
                    $vcoinlog->save();
                    break;
                case "g":
                    $old_gamepoint = $customer->game_point;
                    $new_gamepoint = $old_gamepoint + $rewardvalue;
                    $customer->game_point = $new_gamepoint;
                    $customer->save();

                    $gamepoint = new GamePointLog;
                    //update gamepoinnt log
                    $gamepoint->cus_id = $cus_id;
                    $gamepoint->Credit_amount = $rewardvalue;
                    $gamepoint->remark = 'Lucky Draw Winner';
                    $gamepoint->save();
                    break;
                case "o":

                    break;
                default:
                    return redirect('/');
            }
            //return view for winner
            return view('front.luckydraw.congratulations', ['rewarddesc' => $rewarddescription, 'rewardtype' => $type]);
        }
        else
        {
            //redirect thanks for participating view
            return view('front.luckydraw.thankyou');
        }
        

    }

}
