<?php

namespace App\Repositories;
use App\Models\WithdrawRequest;
use Carbon\Carbon;

class FundRepo
{

	public static function get_fund_transaction_details($mer_id, $input)
	{
        $funds = WithdrawRequest::where('wd_mer_id', '=', $mer_id);

        if (!empty($input['start']) && !empty($input['end'])) {
            $range_type = ($input['type'] != '') ? $input['type'] : 'created_at';
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $funds->where($range_type, '>=', \Helper::TZtoUTC($input['start']));
            $funds->where($range_type, '<=', \Helper::TZtoUTC($input['end']));
        }

        if (!empty($input['id']))
            $funds->where('wd_id', '=', $input['id']);

        if (!empty($input['status']))
            $funds->where('wd_status', '=', $input['status']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'new':
                    $funds->orderBy('created_at', 'desc');
                    break;
                case 'old':
                    $funds->orderBy('created_at', 'asc');
                    break;
                default:
                    $funds->orderBy('created_at', 'desc');
                    break;
            }
        } else {
            $funds->orderBy('created_at', 'desc');
        }

        return $funds->paginate(50);
	}

    public static function check_withdraw_request($id)
    {
        return WithdrawRequest::where('wd_mer_id', '=', $id)->whereIn('wd_status', [0, 1])->first();
    }

    public static function save_withdraw($data)
	{
        $request =  WithdrawRequest::create($data);

        return $request;
	}

    public static function get_funds_by_status($input)
    {
        $funds = WithdrawRequest::select('nm_withdraw_request.*', 'nm_merchant.mer_fname', 'nm_merchant.mer_lname', 'nm_merchant.email', 'nm_merchant.mer_id', 'nm_merchant.mer_vtoken')
        ->LeftJoin('nm_merchant','nm_withdraw_request.wd_mer_id','=','nm_merchant.mer_id')
        ->whereIn('nm_merchant.mer_co_id',isset($input['admin_country_id_list'])? $input['admin_country_id_list'] : '');

        if (!empty($input['start']) && !empty($input['end'])) {
            $range_type = ($input['type'] != '') ? $input['type'] : 'created_at';
            $input['start'] = Carbon::createFromFormat('d/m/Y', $input['start'])->startOfDay()->toDateTimeString();
            $input['end'] = Carbon::createFromFormat('d/m/Y', $input['end'])->endOfDay()->toDateTimeString();

            $funds->where('nm_withdraw_request.'.$range_type, '>=', \Helper::TZtoUTC($input['start']));
            $funds->where('nm_withdraw_request.'.$range_type, '<=', \Helper::TZtoUTC($input['end']));

            if($range_type == 'updated_at') {
                $funds->where('nm_withdraw_request.wd_status','=', 3);
            }
        }
        if (!empty($input['id']))
            $funds->where('nm_merchant.mer_id', '=', $input['id']);

        if (!empty($input['status']) || $input['status'] == '0')
            $funds->where('nm_withdraw_request.wd_status', '=', $input['status']);

        if (!empty($input['sort'])) {
            switch ($input['sort']) {
                case 'new':
                    $funds->orderBy('nm_withdraw_request.wd_date', 'desc');
                    break;
                case 'old':
                    $funds->orderBy('nm_withdraw_request.wd_date', 'asc');
                    break;
                default:
                    $funds->orderBy('nm_withdraw_request.wd_date', 'desc');
                    break;
            }
        } else {
            $funds->orderBy('nm_withdraw_request.created_at', 'desc');
        }

        if(!empty($input['countries']))
            $funds->whereIn('nm_merchant.mer_co_id', $input['countries']);

        if (!empty($input['action']) && $input['action'] == 'export')
            return $funds->get();

        if (!empty($input['name']))
            $funds->where(\DB::raw("CONCAT(mer_fname,' ', mer_lname)"), 'LIKE', '%'.$input['name'].'%');

        if (!empty($input['email']))
            $funds->where('nm_merchant.email', 'LIKE', '%'.$input['email'].'%');


        return $funds->paginate(50);
    }

    public static function update_fund_status($wd_id, $status)
    {
        $update = ['wd_status' => $status];
        if ($status == 3)
            $update = ['wd_status' => $status, 'wd_date' => date('Y-m-d H:i:s')];

        return WithdrawRequest::where('wd_id', '=', $wd_id)->update($update);
    }

    public static function get_fund($wd_id)
    {
        return WithdrawRequest::find($wd_id);
    }

}
