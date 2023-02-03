<?php

namespace App\Http\Controllers\Api\V2\member;

use App\Http\Controllers\Controller;
use App\Repositories\GeneratedCodeRepo;
use App\Models\GeneratedCode;
use Carbon\Carbon;
use Validator;
use Response;
use App;

class EventController extends Controller
{
    protected $niceNames;
    protected $cus_id;
    protected $lang;

    public function __construct()
    {
        $this->lang = "en";

        if (\Auth::guard('api_members')->check()) {
            $this->cus_id = \Auth::guard('api_members')->user()->cus_id;
            $this->secure_code = \Auth::guard('api_members')->user()->payment_secure_code;
        }

        $this->niceNames = [
            'event_name' => trans('api.event_name'),
            'sort' => trans('api.sort'),
            'page' => trans('api.page'),
            'size' => trans('api.size'),
            'status' => trans('api.status'),
            'customer_name' => trans('api.customer_name'),
            'ticket_number' => trans('api.ticket_number'),
            'id' => trans('api.id'),
        ];
    }

    public function ticket_listing()
    {
        $input = \Request::only('id', 'ticket_number', 'status', 'page', 'size', 'sort', 'lang');
        $product_id = !empty($input['id'])? $input['id'] : false;
        $input['status'] = !empty($input['status'])? strtolower($input['status']) : 'all';

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        $v = Validator::make($input, [
            'ticket_number' => 'nullable',
            'status' => 'nullable|in:all,open,cancelled,claimed,expired',
            'id' => 'nullable|integer',
            'page' => 'required|integer',
            'size' => 'required|integer',
            'sort' => 'nullable|in:sdate_asc,sdate_desc,edate_asc,edate_desc',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        $tickets = GeneratedCodeRepo::get_ticket_listing_(false, $this->cus_id, $product_id, $input);
        if($tickets === false) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
            ]);
        }

        $data = [];
        foreach ($tickets as $ticket) {

            $attributes = $ticket->order_attributes;
            if(!empty($attributes)) {
                $new = [];
                foreach (json_decode($attributes, true) as $a => $b) {
                    $new[] = trim($a).' '.trim($b);
                }
                $attributes = implode(', ', $new);
            } else {
                $attributes = null;
            }

            $data[] = [
                'id' => $ticket->id,
                'event_name' => $ticket->event_name,
                'image' => $ticket->image,
                'start_date' => $ticket->start_date,
                'end_date' => $ticket->end_date,
                'option' => $attributes,
                'ticket_number' => $ticket->serial_number,
                'status' => $ticket->status,
            ];
        }

        return Response::json([
            'status' => 200,
            'message' => trans('api.successRetrieve'),
            'total' => $tickets->total(),
            'current_page' => $tickets->currentPage(),
            'total_pages' => $tickets->lastPage(),
            'data' => $data,
        ]);
    }
}