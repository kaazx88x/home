<?php

namespace App\Repositories;

use App\Models\SecurityQuestion;
use App\Models\Customer;
use DB;

class SecurityQuestionRepo
{
    public static function all()
    {
        $questions = SecurityQuestion::all();
            return $questions;
    }

    public static function update_customer_security($cus_id, $data)
    {
        $customer = Customer::find($cus_id);
        if($customer) {
            $customer->question_1 = $data['security_question_1'];
            $customer->question_2 = $data['security_question_2'];
            $customer->question_3 = $data['security_question_3'];
            $customer->answer_1 = $data['security_answer_1'];
            $customer->answer_2 = $data['security_answer_2'];
            $customer->answer_3 = $data['security_answer_3'];

            $customer->save();

            return true;
        }

        return false;
    }
}