<?php

namespace App\Http\Controllers\Api\V2\merchant;

use App\Http\Controllers\Controller;
use App\Repositories\GeneratedCodeRepo;
use App\Models\Product;
use Carbon\Carbon;
use Validator;
use Response;
use App;

class EventController extends Controller
{
    protected $niceNames;
    protected $mer_id;
    protected $app_session;

    public function __construct()
    {
        if (\Auth::guard('api_storeusers')->check()) {
            $this->mer_id = \Auth::guard('api_storeusers')->user()->mer_id;
            $this->app_session = \Auth::guard('api_storeusers')->user()->app_session;
        }

        if (\Auth::guard('api_merchants')->check()) {
            $this->mer_id = \Auth::guard('api_merchants')->user()->mer_id;
            $this->app_session = \Auth::guard('api_merchants')->user()->app_session;
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

    public function events()
    {
        $input = \Request::only('event_name', 'sort', 'page', 'size', 'lang');

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $v = Validator::make($input, [
            'event_name' => 'nullable',
            'page' => 'required|integer',
            'size' => 'required|integer',
            'sort' => 'nullable|in:sdate_asc,sdate_desc,edate_asc,edate_desc,purchased_asc,purchased_desc',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        $events = GeneratedCodeRepo::get_event_listing($this->mer_id, $input);
        if(!$events) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
            ]);
        }

        return Response::json([
            'status' => 200,
            'message' => trans('api.successRetrieve'),
            'total' => $events->total(),
            'current_page' => $events->currentPage(),
            'total_pages' => $events->lastPage(),
            'data' => $events->items(),
        ]);
    }

    public function ticket_listing()
    {
        $input = \Request::only('id', 'status', 'customer_name', 'ticket_number', 'lang');
        $product_id = !empty($input['id'])? $input['id'] : false;
        $input['status'] = !empty($input['status'])? strtolower($input['status']) : 'all';

        if (isset($input['lang'])) {
            App::setLocale($input['lang']);
        }
        unset($input['lang']);

        // input validation
        $v = Validator::make($input, [
            'id' => 'required|integer',
            'status' => 'nullable|in:all,open,cancelled,claimed,expired',
            'customer_name' => 'nullable',
            'ticket_number' => 'nullable',
        ])->setAttributeNames($this->niceNames);

        if ($v->fails()) {
            return \Response::json([
                'status' => 422,
                'message' => implode("\n", $v->errors()->all())
            ], 422);
        }

        $event = Product::selectRaw("pro_id as id, pro_title_en as event_name, (SELECT CONCAT(?,?,?,?,nm_product.pro_mr_id,?,image) FROM nm_product_image WHERE pro_id = nm_product.pro_id ORDER BY nm_product_image.order ASC, nm_product_image.main ASC LIMIT 1) as image, start_date, end_date, (pro_qty + pro_no_of_purchase) as total_seat, pro_no_of_purchase as purchased",
        [env('IMAGE_DIR'),'/','product','/','/'])
        ->where('pro_id', $input['id'])
        ->where('pro_type', 3)
        ->where('pro_status', 1)
        ->where('pro_mr_id', $this->mer_id)
        ->first();
        if(!$event) {
            return \Response::json([
                'status' => 404,
                'message' => trans('api.event') . trans('api.notFound'),
            ], 404);
        }

        $tickets = GeneratedCodeRepo::get_ticket_listing_($this->mer_id, false, $product_id, $input);
        if($tickets === false) {
            return \Response::json([
                'status' => 500,
                'message' => trans('api.systemError')
            ]);
        }

        $ticket_listings = $tickets->groupBy('order_attributes');
        $data = [];
        foreach ($ticket_listings as $attribute => $options) {

            $listing = [];
            foreach ($options as $ticket) {
                $listing[] = [
                    'ticket_number' => $ticket->serial_number,
                    'customer_name' => $ticket->customer_name,
                    'status' => $ticket->status,
                ];
            }

            if(!empty($attribute)) {
                $new = [];
                foreach (json_decode($attribute, true) as $a => $b) {
                    $new[] = trim($a).' '.trim($b);
                }
                $attribute = implode(', ', $new);
            } else {
                $attribute = null;
            }

            $data[] = [
                'options' => $attribute,
                'data' => $listing,
            ];
        }

        return Response::json([
            'status' => 200,
            'message' => trans('api.successRetrieve'),
            'data' => [
                'event' => $event,
                'tickets' => $data
            ],
        ]);
    }
}