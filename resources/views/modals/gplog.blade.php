<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="row">
        <div class="col-sm-3"><img src="{{ url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:35px;"></div>
        <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3>Game Point Log</h3></div>
    </div>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-sm-12">
            <table border=0 width="100%">
                <tr>
                    <th>Customer Name</td>
                    <th>:</th>
                    <td> {{$customer->cus_name}}</td>
                </tr>
                <tr>
                    <th>Game Point</td>
                    <th>:</th>
                    <td> {{$customer->game_point}}</td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="table-responsive col-sm-12">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">No.</th>
                        <th>Debit</th>
                        <th class="text-center">Credit</th>
                        <th class="text-center">Remarks</th>
                        <th class="text-center">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    @foreach($gp as $gps)
                    <tr class="text-center">
                        <td>{{ $i }}</td>
                        <td>{{ $gps->Debit_amount}}</td>
                        <td>{{ $gps->Credit_amount}}</td>
                        <td>{{ $gps->remark}}</td>
                        {{-- <td>{{ date('d F Y H:i:s', strtotime($gps->created_at)) }}</td> --}}
                        <td>{{ \Helper::UTCtoTZ($gps->created_at) }}</td>
                    </tr>
                    <?php $i++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button data-dismiss="modal" class="btn btn-default" type="button">{{trans('localize.close_')}}</button>
</div>
