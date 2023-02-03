<?php

namespace App\Http\Controllers\Merchant;

use App\Http\Controllers\Merchant\Controller;
use App\Repositories\FundRepo;
use App\Repositories\MerchantRepo;
use App\Repositories\S3ClientRepo;
use Validator;

class FundController extends Controller
{
    public function __construct(FundRepo $fundrepo, MerchantRepo $merchantrepo)
    {
        $this->fund = $fundrepo;
        $this->merchant = $merchantrepo;
    }

    public function fund_report()
	{
        $mer_id = \Auth::guard('merchants')->user()->mer_id;
        $input = \Request::only('id', 'start', 'end', 'type', 'status', 'sort');
        $status_list = array(
            ''  => trans('localize.all'),
            '0' => trans('localize.pending'),
            '1' => trans('localize.approved'),
            '2' => trans('localize.declined'),
            '3' => trans('localize.paid'),
        );

        $funds = $this->fund->get_fund_transaction_details($mer_id, $input);

        return view('merchant.fund.report', compact('funds', 'status_list', 'input'));
	}

    public function fund_withdraw()
    {
        $mer_id = \Auth::guard('merchants')->user()->mer_id;

        $vt_balance = $this->merchant->get_merchant_vtoken($mer_id);
        $commission_amt = $this->merchant->get_merchant_commison($mer_id);
        $requested = $this->fund->check_withdraw_request($mer_id);
        $merchant = $this->merchant->get_merchant_profile_details($mer_id);

        if (!$merchant->bank_acc_no || !$merchant->bank_acc_name || !$merchant->bank_name || !$merchant->bank_country) {
            return redirect('merchant/profile/edit')->with('error', trans('localize.bankInfoCompulsary'));
        }

        return view('merchant.fund.withdraw', compact('vt_balance', 'commission_amt', 'requested', 'merchant'));
	}

    public function fund_withdraw_submit()
    {
        $merchant = \Auth::guard('merchants')->user();
        $mer_id = $merchant->mer_id;

        $requested = $this->fund->check_withdraw_request($mer_id);
        if ($requested)
            return back()->withErrors(trans('localize.cantprocessfund'));

        if (\Request::isMethod('post')) {
            $data = \Request::all();
            Validator::make($data, [
                'pay' => 'required|numeric|between:0.0001,'.max($merchant->mer_vtoken, 0),
                'file' => 'required|mimes:jpeg,jpg,png,pdf|max:2000',
            ])->validate();

            $file_name = null;
            if(!empty($data['file'])) {
                $upload_file = $data['file']->getClientOriginalName();
                $file_detail = explode('.', $upload_file);
                $file_name = time().'_'.str_random(4).'.'.$file_detail[1];
                $path = "fund/statement/$merchant->mer_id";

                if(@file_get_contents($data['file']) && !S3ClientRepo::IsExisted($path, $file_name))
                    $upload = S3ClientRepo::Upload($path, $data['file'], $file_name);
            }

            $vt_balance = $this->merchant->get_merchant_vtoken($mer_id);
            $commission_amt = $this->merchant->get_merchant_commison($mer_id);
            $merchant = $this->merchant->get_merchant_profile_details($mer_id);

            // $commission = ($merchant->mer_type != 1) ? (floatval($commission_amt) / 100) : 0;
            $commission = 0;
            $admin_commission = ($data['pay'] * $commission);
            $balance = ($vt_balance - ($data['pay'] + $admin_commission));

            $input = [
                'wd_mer_id' => $mer_id,
                'wd_total_wd_amt' => $vt_balance,
                'wd_submited_wd_amt' => $data['pay'],
                'wd_balance_after' => $balance,
                'wd_admin_comm_amt' => $admin_commission,
                'wd_currency' => $merchant->co_curcode,
                'wd_rate' => ($merchant->mer_type == 1) ? $merchant->co_offline_rate : $merchant->co_rate,
                'wd_statement' => $file_name,
            ];

            try {
                $this->fund->save_withdraw($input);
            } catch (\Exception $e) {
                return back()->withErrors(trans('localize.fundfailed'));
            }

            return redirect('merchant/fund/report')->with('success', trans('localize.fundsuccess'));
        }
    }
}
