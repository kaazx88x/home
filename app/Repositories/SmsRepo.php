<?php
namespace App\Repositories;

use App\Sms\SmsService;
use App\Models\TacVerificationLog;

class SmsRepo
{
    public static function check_tac_exist($phone, $action, $type, $email)
    {
        $check = TacVerificationLog::where('expired_at', '>', date('Y-m-d H:i:s'))
        ->where('is_verified', 0)
        ->where('sms_status', '<', 2)
        ->where('action', $action)
        ->where(function ($q) use ($phone, $email) {
            $q->where('phone', $phone)->orWhere('email', $email);
        });

        return $check->first();
    }

    public static function create_tac_log($action, $cus_id, $type = 'sms', $phone = null, $email = null)
    {
        // check db if already got tac that not expired dont create new one
        $tac_exist = SmsRepo::check_tac_exist($phone, $action, $type, $email);

        if ($tac_exist) {
            if ((($type == 'sms') && ($tac_exist->phone == $phone)) || (($type == 'email') && ($tac_exist->email == $email))) {
                if ($type == 'email') {
                    $message = trans('localize.tacsuccess_email', [
                        'date' => \Helper::UTCtoTZ(date('Y-m-d H:i:s')),
                        'email' => \Helper::secureEmail($tac_exist->email)
                    ]);
                } else {
                    $message = trans('localize.tacsuccess', [
                        'date' => \Helper::UTCtoTZ(date('Y-m-d H:i:s')),
                        'phone' => \Helper::securePhone($tac_exist->phone)
                    ]);
                }

                return [
                    'status' => 1,
                    'message' => $message,
                    'expired_at' => $tac_exist->expired_at
                ];
            } else {
                $update_tac_log = TacVerificationLog::where('id', $tac_exist->id)
                ->update([
                    'is_verified' => 2
                ]);
            }
        }

        $tac = mt_rand(100000,999999);

        $now = \Carbon\Carbon::now();
        $expired = $now->addMinutes(60)->format('Y-m-d H:i:s');

        $tac_log = TacVerificationLog::create([
            'customer_id' => $cus_id,
            'tac' => $tac,
            'phone' => ($type == 'sms') ? $phone : null,
            'email' => ($type == 'email') ? $email : null,
            'action' => $action,
            'expired_at' => $expired,
            'sms_content' => trans('localize.tacsms', [
                'tac' => $tac,
                'action' => trans('localize.'.$action),
                'date' => date('Y-m-d H:i:s'),
            ]),
        ]);

        if ($type == 'email') {
            \Mail::send('front.emails.tac_email', ['content' => $tac_log->sms_content], function ($message) use ($tac_log) {
                $message->to($tac_log->email)->subject(trans('localize.'.$tac_log->action). ' - Verification Code');
            });
            $send_tac = true;
            if( count(\Mail::failures()) > 0 ) {
                $send_tac = false;
            }
        } else {
            $send_tac = SmsService::send($tac_log->phone, $tac_log->sms_content, $tac_log->tac);
        }

        if (!$send_tac) {
            $update_tac_log = TacVerificationLog::where('id', $tac_log->id)
            ->update([
                'sms_status' => 2
            ]);

            return [
                'status' => 0,
                'message' => trans('localize.tacfail')
            ];
        }

        $update_tac_log = TacVerificationLog::where('id', $tac_log->id)
        ->update([
            'sms_status' => 1
        ]);

        if ($type == 'email') {
            $message = trans('localize.tacsuccess_email', [
                'date' => \Helper::UTCtoTZ(date('Y-m-d H:i:s')),
                'email' => \Helper::secureEmail($tac_log->email)
            ]);
        } else {
            $message = trans('localize.tacsuccess', [
                'date' => \Helper::UTCtoTZ(date('Y-m-d H:i:s')),
                'phone' => \Helper::securePhone($tac_log->phone)
            ]);
        }

        return [
            'status' => 1,
            'message' => $message,
            'expired_at' => $tac_log->expired_at
        ];
    }

    public static function check_tac($cus_id = 0, $tac, $action, $flag, $phone = null, $email = null)
    {
        $check = TacVerificationLog::where('customer_id', $cus_id)
        ->where('tac', $tac)
        ->where('expired_at', '>', date('Y-m-d H:i:s'))
        ->where('is_verified', 0)
        ->where('sms_status', 1)
        ->where('action', $action)
        ->where(function ($q) use ($phone, $email) {
            $q->where('phone', $phone)->orWhere('email', $email);
        })
        ->first();

        if ($check) {
            if($flag)
                $update_tac_log = TacVerificationLog::where('id', $check->id)->update(['is_verified' => 1]);
            return true;
        }

        return false;
    }

    public static function send_direct_sms($cus_id, $phone, $action, $data)
    {
        try {

            $sms_content = '';
            switch ($action) {
                case 'admin_register_customer':
                    $sms_content = trans('localize.sms.phone.register', ['name' => $data['name']]);
                    break;

                case 'admin_update_phone':
                    $sms_content = trans('localize.sms.phone.update', ['phone' => $data['phone']]);
                    break;

                default:
                    return false;
                    break;
            }

            $log = TacVerificationLog::create([
                'customer_id' => $cus_id,
                'tac' => null,
                'phone' => $phone,
                'email' => null,
                'action' => $action,
                'expired_at' => null,
                'sms_content' => $sms_content,
                'is_verified' => 1,
                'sms_status' => 1,
            ]);

            $send = SmsService::send($phone, $sms_content, null);
            if(!$send) {
                $update_tac_log = TacVerificationLog::where('id', $log->id)
                ->update([
                    'sms_status' => 2
                ]);

                return false;
            }

            return true;

        } catch (Exception $e) {
            return false;
        }
    }
}