<?php
namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Admin\Controller;
use App\Models\LuckyDraw;

class LuckyDrawController extends Controller
{
    public function __construct() {
    }

    public function manage()
    {
        $input = \Request::only('email', 'phone', 'status');
        $luckydraws = LuckyDraw::leftJoin('nm_customer', 'nm_customer.cus_id', '=', 'lucky_draws.claim_by');

        if (!empty($input['email']))
            $luckydraws = $luckydraws->where('nm_customer.email', 'LIKE', '%'.$input['email'].'%');

        if (!empty($input['phone']))
            $luckydraws = $luckydraws->where(\DB::raw("CONCAT(nm_customer.phone_area_code, '', nm_customer.cus_phone)"), 'LIKE', '%'.$input['phone'].'%');

        if (!empty($input['status'])) {
            switch ($input['status']) {
                case 'unclaimed':
                    $luckydraws = $luckydraws->whereNull('lucky_draws.claim_by');
                    break;
                case 'unredeem':
                    $luckydraws = $luckydraws->whereNotNull('lucky_draws.claim_by')->where('lucky_draws.status', 0);
                    break;
                case 'redeemed':
                    $luckydraws = $luckydraws->whereNotNull('lucky_draws.claim_by')->where('lucky_draws.status', 1);
                    break;
                default:
                    break;
            }
        }

        $luckydraws = $luckydraws->paginate(50);

        return view('admin.lucky_draw.manage', compact('luckydraws', 'input'));
    }

    public function print_all()
    {
        $luckydraws = LuckyDraw::all();

        return view('admin.lucky_draw.print_all', compact('luckydraws'));
    }

    public function print_dummy()
    {
        $input = \Request::only('total');
        $total = ($input['total']) ? $input['total'] : 1;

        return view('admin.lucky_draw.print_dummy', compact('total'));
    }

    public function redeemed($id)
    {
        $update = LuckyDraw::where('id', $id)->update([
            'status' => 1,
            'redemption_date' => date('Y-m-d H:i:s')
        ]);

        return \redirect('admin/lucky_draw/manage');
    }




}
