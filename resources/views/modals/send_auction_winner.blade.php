<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>{{trans('localize.sendauctiontowinner')}}</h4>
</div>
<div class="modal-body">
    <form class="form-horizontal" action="{{ url('send_auction_winner') }}" method="post">
        {{csrf_field()}}
    <input type="hidden" name="oa_id" value="{{ $oa_id }}">
    <div class="row">
        <div class="form-group">
            <label class="col-lg-3 control-label">{{trans('localize.selectstatus')}}</label>
            <div class="col-lg-9">
                <div class="btn-group" data-toggle="buttons">
                    <label class="btn btn-outline btn-warning active">
                        <input type="radio" value="3" name="status" autocomplete="off" checked> {{trans('localize.cancel')}}
                    </label>
                    <label class="btn btn-outline btn-primary">
                        <input type="radio" value="1" name="status" autocomplete="off"> {{trans('localize.senditem')}}
                    </label>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-lg-3 control-label">{{trans('localize.date')}}</label>
            <div class="col-lg-9">
                <input class="form-control" type="text" name="date" value="{{  date('Y-m-d H:i:s') }}" readonly >
            </div>
        </div>
    </div>
    <div class="row">
        <button type="submit" class="btn btn-primary btn-sm pull-right">{{trans('localize.submit')}}</button>
    </div>
    </form>
</div>
<script>

</script>