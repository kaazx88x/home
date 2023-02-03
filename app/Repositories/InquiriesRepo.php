<?php

namespace App\Repositories;
use App\Models\Inquiries;

class InquiriesRepo
{
    public static function all($input)
    {
        $inquiries = Inquiries::orderBy('created_at', 'desc');

        if (!empty($input['search'])) {
            $inquiries->where('iq_name', 'LIKE', '%'.$input['search'].'%')
            ->orWhere('iq_emailid', 'LIKE', '%'.$input['search'].'%')
            ->orWhere('iq_phonenumber', 'LIKE', '%'.$input['search'].'%');
        }

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'new':
                    $inquiries->orderBy('created_at', 'desc');
                    break;
                case 'old':
                    $inquiries->orderBy('created_at', 'asc');
                    break;
            }
        }

        return $inquiries->paginate(20);
    }
}
