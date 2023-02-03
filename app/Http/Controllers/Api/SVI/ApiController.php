<?php

namespace App\Http\Controllers\Api\SVI;

use App;
use App\Http\Controllers\Controller;
use App\Models\TopupRequest;
use App\Models\Customer;
use App\Models\VcoinLog;
use App\Models\GamePointLog;
use App\Models\Wallet;
use App\Models\CustomerWallet;
use Request;
use Response;

class ApiController extends Controller
{
    public function topup($type)
    {
        $data = \Request::only('key', 'secret', 'email', 'ref', 'credit', 'gpoint', 'from', 'wallet', 'secureHash', 'transactionref', 'special_wallet');
        $data["ip"] = $this->GetIp();
        $data["type"] = $type == 'vt_topup' ? "V" : "G";
        $data["ref"] = $data["ref"].'-'.md5(time().mt_rand(10,99));
        $data["status"] = 0;
        $data["vtoken"] = $data["credit"];

        $toprequest = TopupRequest::create($data);

        if (!$this->CheckPartner($data['key'], $data['secret'], $data["ip"]))
        {
            return Response::json(
                array(
                    'status' => '0',
                    'message' => 'Wrong API Secret Key IP for Partner.',
                ), 200
            );
        }

        $secureHash = "";
        if ($data["type"] == "V")
            $secureHash = md5($data['email'] . (string) $data['credit'] . $data['from'] . $data['wallet'] . 'Qwe&89))(^&');
        if ($data["type"] == "G")
            $secureHash = md5($data['email'] . (string) $data['gpoint'] . 'Qwe&89))(^&');

        if ($secureHash != $data['secureHash'])
        {
            return Response::json(
                array(
                    'status' => '0',
                    'message' => 'Secure key does not match',
                ), 200
            );
        }

        $cust_details = \DB::table('nm_customer')
        ->where('email', $data['email'])->where('cus_status', 1)
        ->first();

        if (!$cust_details)
        {
            return Response::json(
                array(
                    'status' => '0',
                    'message' => 'Email didn\'t exist in the system.',
                ), 200
            );
        }

        $value = $data["type"] == "V" ? $data['credit'] : $data['gpoint'];
        if (!is_numeric($value) || $value <= 0)
        {
            return Response::json(
                array(
                    'status' => '0',
                    'message' => 'Topup value is not valid.',
                ), 200
            );
        }
        $toprequest->status = 1;
        $toprequest->save();
        return Response::json(
            array(
                'status' => '1',
                'message' => 'Request is accepted',
                'value' => $value,
                'ref' => $toprequest->ref
            ), 200
        );
    }

    public function confirm()
    {
        try
        {

            $data = \Request::only('key', 'secret', 'ref');
            $ipaddress = $this->GetIp();
            $partner = $this->CheckPartner($data['key'], $data['secret'], $ipaddress);
            if (!$partner)
            {
                return Response::json(
                    array(
                        'status' => '0',
                        'message' => 'Wrong API Secret Key IP for Partner.',
                    ), 200
                );
            }

            $toprequest = TopupRequest::where('ref', $data['ref'])->where('status', 1)->first();
            if (!$toprequest)
            {
                return Response::json(
                    array(
                        'status' => 0,
                        "message" => "record not found"
                    ), 200
                );
            }

            $toprequest->status = 2;
            $toprequest->save();
            $cust_details = Customer::where('email', $toprequest->email)->first();

            if ($toprequest->type == 'V')
            {
                $cust_details->v_token += $toprequest->vtoken;
                $cust_details->save();

                if ($toprequest->special_wallet) {
                    $wallet_id = ($toprequest->special_wallet == 1) ? 3 : 4;
                    $wallet_remark = ($toprequest->special_wallet == 1) ? 'AP' : 'AP-2';

                    $wallet_distribution = $toprequest->vtoken;
                    $customer_wallet =  CustomerWallet::where('customer_id', $cust_details->cus_id)->where('wallet_id', $wallet_id)->first();
                    if ($customer_wallet) {
                        $wallet_distribution = $customer_wallet->credit + $toprequest->vtoken;
                    }

                    $cus_wallet = CustomerWallet::updateOrCreate(
                        [
                            'customer_id' => $cust_details->cus_id,
                            'wallet_id' => $wallet_id
                        ],
                        [
                            'credit' => $wallet_distribution
                        ]
                    );

                    $logs = VcoinLog::create([
                        'cus_id' => $cust_details->cus_id,
                        'credit_amount' => $toprequest->vtoken,
                        'remark' => $partner->name . ' : Credit Top-up : ' . $wallet_remark . ' : ' . $toprequest->vtoken,
                        'from' => $toprequest->from,
                        'svi_wallet' => $toprequest->wallet,
                        'transactionref' => $toprequest->transactionref,
                        'wallet_id' => $wallet_id
                    ]);
                }
                else {
                    $wallets = Wallet::where('percentage', '>', 0)->get();
                    foreach ($wallets as $wallet) {
                        $wallet_add = round(($toprequest->vtoken * ($wallet->percentage/100)), 4);

                        $customer_wallet =  CustomerWallet::where('customer_id', $cust_details->cus_id)->where('wallet_id', $wallet->id)->first();

                        $wallet_distribution = $wallet_add;
                        if ($customer_wallet) {
                            $wallet_distribution = $customer_wallet->credit + $wallet_add;
                        }

                        $cus_wallet = CustomerWallet::updateOrCreate(
                            [
                                'customer_id' => $cust_details->cus_id,
                                'wallet_id' => $wallet->id
                            ],
                            [
                                'credit' => $wallet_distribution
                            ]
                        );

                        $logs = VcoinLog::create([
                            'cus_id' => $cust_details->cus_id,
                            'credit_amount' => $wallet_add,
                            'remark' => $partner->name . ' : Credit Top-up : ' . $wallet->name . ' : ' . $wallet_add,
                            'from' => $toprequest->from,
                            'svi_wallet' => $toprequest->wallet,
                            'transactionref' => $toprequest->transactionref,
                            'wallet_id' => $wallet->id
                        ]);
                    }
                }

                // $log = new VcoinLog();
                // $log->cus_id = $cust_details->cus_id;
                // $log->credit_amount = $toprequest->vtoken;
                // $log->remark = $partner->name . ' : Credit Top-up : ' . $toprequest->vtoken;
                // $log->from = $toprequest->from;
                // $log->svi_wallet = $toprequest->wallet;
                // $log->transactionref = $toprequest->transactionref;
                // $log->save();
            }
            // if ($toprequest->type == 'G')
            // {
            //     $cust_details->game_point += $toprequest->gpoint;
            //     $cust_details->save();
            //     $log = new GamePointLog();
            //     $log->cus_id = $cust_details->cus_id;
            //     $log->Credit_amount = $toprequest->gpoint;
            //     $log->remark = $partner->name . ' : Game Point Top-up : ' . $toprequest->gpoint;
            //     $log->save();
            // }

            try
            {
                $type = ($toprequest->type == 'V') ? "mei_point" : "game_point";
                if ($toprequest->special_wallet) {
                    $type = ($toprequest->special_wallet == 1) ? "ap_point" : "ap2_point";
                }

                $data = array(
                    'email' => $cust_details->email,
                    'name' => $cust_details->cus_name,
                    'totalvalue' => $toprequest->type == 'V'?$cust_details->v_token:$cust_details->game_point,
                    'addedvalue' => $toprequest->type == 'V'?$toprequest->vtoken:$toprequest->gpoint,
                    'type' => $type,
                    'from' => $toprequest->from,
                    'wallets' => $cust_details->customer_wallets,
                );

                \Mail::send('front.emails.topup', $data, function ($message) use ($cust_details, $type) {
                    $message->to($cust_details->email, $cust_details->cus_name)->subject(trans('localize.topup_content.type.' . $type) . ' Topped Up Successfully');
                });
            }
            catch (\Exception $e)
            {

            }

            return Response::json(
                array(
                    'status' => 1,
                    'message' => 'Value is topup.',
                    'type' => $toprequest->type == "V" ? "Credit" : "Game Point",
                    'value' => $toprequest->type == "V" ? $toprequest->vtoken : $toprequest->gpoint,
                ), 200
            );


        }
        catch (Exception $ex)
        {
            return Response::json(
                array(
                    'status' => 0,
                    'message' => 'unknown error.'
                ), 200
            );
        }
    }

    protected function CheckPartner($key, $secret, $ipaddress)
    {
        $iparray = array("127.0.0.1", "110.4.41.33", "103.18.246.34");

        if(!in_array($ipaddress, $iparray))
        {
            return false;
        }

        $check_partner = \DB::table('sv_partners')
        ->where('api_key', $key)
        ->where('api_secret', $secret)
        // ->where('ip', $ipaddress)
        ->first();

        if ($check_partner)
            return $check_partner;
        else
            return false;
    }

    protected function GetIp()
    {
        $ipaddress = '';
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if (isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if (isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if (isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';

        return $ipaddress;
    }

}
