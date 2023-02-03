<?php

namespace App\Sms;

use App\Sms\ISms;
use App\Sms\juhesms;

class SmsService {
    private $isms;

    public function __construct()
    {
        $this->isms = new ISms();
        $this->juhesms = new juhesms();
    }

    public static function send($mobile_no, $message, $tac)
    {
        // $ischina = substr($mobile_no, 0, 2)=='86'?true:false;
        $ischina = false;

        if($ischina)
        { \Log::info( 'china');
            $juhesms = new juhesms;
            $sent_flag = $juhesms->send(substr($mobile_no, 2), $tac);
        }
        else
        {\Log::info('not china');
            $isms = new ISms;
            $sent_flag = $isms->send($mobile_no, $message);
        }

        // reserved for secondary sms provider
        // if ($sent_flag === false) {

        // }

        return $sent_flag;
    }
}