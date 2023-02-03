<?php

namespace App\Models;

use App;
use Illuminate\Database\Eloquent\Model;

class SecurityQuestion extends Model
{
    protected $table = 'account_security_question';
    protected $primaryKey = 'id';

    public function getQuestionAttribute($value)
    {
        $default = $this->{'question_en'};
        $title = $this->{'question_'.App::getLocale()};

        if (empty($title)) {
            return $default;
        } else {
            return $title;
        }
    }
}
