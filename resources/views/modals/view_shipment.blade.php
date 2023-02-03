<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>@lang('localize.shipmentdetail')</h4>
</div>

<div class="modal-body">
    <div class="row">
        <label class="col-sm-4"><i class=" icon-truck"></i>@lang('localize.corier')</label>
        {{ ($courier->order_courier_id == 0)? trans('localize.self_pickup') : $courier->name }}
    </div>
    @if($courier->order_courier_id > 0)
    <div class="row">
        <label class="col-sm-4"><i class=" icon-search"></i>@lang('localize.trackingno').</label>
        {{ $courier->order_tracking_no }}
    </div>
    <div class="row">
        <label class="col-sm-4"><i class=" icon-link"></i>@lang('localize.trackingwebsite')</label>
        {{ $courier->link }}
        <br/>
        <a href="{{ $courier->link }}" target="_blank" class="btn btn-sm btn-white"><i class="fa fa-external-link"></i>@lang('localize.openlink')</a>
    </div>
    @else
    <div class="row">
        <label class="col-sm-4"><i class=" icon-search"></i>@lang('localize.remarks').</label>
        {{ $courier->order_tracking_no }}
    </div>
    @endif
</div>
