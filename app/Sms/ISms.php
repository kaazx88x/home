<?php

namespace App\Sms;

use GuzzleHttp\Client;

class ISms {
    private $url;
    private $username;
    private $password;

    function __construct()
    {
        $this->url = 'http://www.isms.com.my/isms_send.php';
        $username = env('ISMS_USERNAME');
        $password = env('ISMS_PASSWORD');
        $type = 1;

        $current_lang = app()->getLocale();
        if ($current_lang) {
            if ($current_lang !== 'en' && $current_lang !== 'ms' && $current_lang !== 'id') {
                $type = 2;
            }
        }

        $this->data = [
                        'un' => $username,
                        'pwd' => $password,
                        'type' => $type // 1 - ASCII, 2 - UTF8
                    ];

    }

    public function send($mobile_no, $message)
    {
        $this->data['dstno'] = $mobile_no;
        $this->data['msg'] = rawurlencode($message);

        $retry_count = config('tac.retry_send_count');

        $client = new Client();

        do {
            $response = $client->post($this->url, [
                'form_params' => $this->data
            ]);

            $status_code = $response->getStatusCode();
            $body = $response->getBody()->getContents();

            if ($status_code === 200 && $body === '2000 = SUCCESS') {
                return true;
            }

            $retry_count -= 1;

        } while ($retry_count > 0 && $retry_count <= 3);

        return false;
    }
}