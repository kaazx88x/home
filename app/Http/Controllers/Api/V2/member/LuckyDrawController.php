<?php

namespace App\Http\Controllers\Api\V2\member;

use App\Http\Controllers\Controller;
use App\Models\LuckyDraw;
use App\Models\Customer;
use App\Models\CustomerVTokenLog;
use App;
use Validator;

class LuckyDrawController extends Controller
{
    // protected $customer;
    protected $cus_id;

    public function __construct()
    {
        if (\Auth::guard('api_members')->check()) {
            $this->cus_id = \Auth::guard('api_members')->user()->cus_id;
        }
    }

    public function redeem()
    {
        $input = \Request::only('code', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $niceNames = array(
            'code' => trans('localize.code'),
        );

        $validator = Validator::make($input, [
            'code' => 'required',
        ]);
        $validator->setAttributeNames($niceNames);

        if ($validator->fails())
        {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $validator->errors()->all())
            ]);
        }

        // Check customer exist
        $customer = Customer::find($this->cus_id);
        if (empty($customer))
        {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.memberId') . $this->cus_id . trans('api.notFound')
            ]);
        }

        // Check customer has redeem history or not
        $redeem = LuckyDraw::where('claim_by', $this->cus_id)->first();
        if ($redeem)
        {
            return \Response::json([
                'status' => 422,
                'message' => "You have already claimed your lucky draw.\n您已扫描过这次的幸运抽奖."
            ]);
        }

        // Redeem code
        $luckydraw = LuckyDraw::where('code', $input['code'])->first();
        if ($luckydraw)
        {
            if ($luckydraw->claim_by)
            {
                return \Response::json([
                    'status' => 200,
                    'message' => "Lucky draw code you scanned has already been claimed.\n您扫描的幸运抽奖代码已被兑换."
                ]);
            }

            $dt = new \DateTime();
            $dt->format('Y-m-d H:i:s');
            $luckydraw->claim_by = $this->cus_id;
            $luckydraw->claim_date = $dt;
            $luckydraw->save();

            if ($luckydraw->type < 1) {
                $old_vtoken = $customer->v_token;
                $new_vtoken = $old_vtoken + $luckydraw->value;
                $customer->v_token = $new_vtoken;
                $customer->save();

                $arrCustomerVTokenLog = [
                    'cus_id' => $this->cus_id,
                    'credit_amount' => $luckydraw->value,
                    'debit_amount' => 0,
                    'remark' => 'Lucky Draw Winner'
                ];
                CustomerVTokenLog::firstOrCreate($arrCustomerVTokenLog);
            }

            return \Response::json([
                'status' => 200,
                // 'message' => trans('localize.congratulation'),
                // 'reward_description' => $luckydraw->desc . (($luckydraw->type == 2) ? trans('localize.redeem_stage') : trans('localize.redeem_counter'))
                'message' => "Congratulation! You've won\n恭喜！你赢了",
                'reward_description' => "\n" . $luckydraw->desc . (($luckydraw->type == 2) ? "\n\nPlease go on the stage to redeem your prize.\n请上台兑换奖品." : "\n\nPlease go to the prize redemption counter to redeem your prize.\n请到奖品兑换柜台兑换奖品.")
            ]);
        }

        return \Response::json([
            'status' => 200,
            'message' => "Thank you for your participation.\n感谢您的参与."
        ]);
    }
}