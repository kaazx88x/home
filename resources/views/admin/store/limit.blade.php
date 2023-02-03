@extends('admin.layouts.master')

@section('title', 'Store Limit')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.store_limit')}}</h2>
        <ol class="breadcrumb">
            <li>
                @lang('localize.Merchant')
            </li>
            <li>
                @lang('localize.store')
            </li>
            <li class="active">
                <strong>@lang('localize.limit')</strong>
            </li>
        </ol>
    </div>
</div>
<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">

            @include('admin.common.notifications')
            <div class="row">
                @foreach($transactions as $key => $trans)
                <div class="col-lg-3">
                    <div class="ibox float-e-margins">
                        <div class="ibox-title">
                            <h5>{{ $types[$key] }}</h5>
                            <span class="label label-{{ ($trans['block_amount'] || $trans['block_count'])? 'warning' : 'success' }} pull-right">{{ ($trans['block_amount'] || $trans['block_count'])? trans('localize.limited') :  trans('localize.unlimited') }}</span>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-12">
                                    @if($trans['block_amount'])
                                    <h2 class="no-margins"><small>{{ $store->country? $store->country->co_curcode : '' }}</small> {{ number_format($trans['current_amount'], 2) }} / {{ number_format($trans['limit_amount_exceed'], 2) }}</h2>
                                    <div class="stat-percent font-bold text-{{ ($trans['limit_amount_usage'] >= 100)? 'danger' : 'info' }}">{{ $trans['limit_amount_usage'] }} %</div> {{trans('localize.total_transaction')}}
                                    @else
                                    <h2 class="no-margins"><small>{{ $store->country? $store->country->co_curcode : '' }}</small> {{ number_format($trans['current_amount'], 2) }}</h2> {{trans('localize.total_transaction')}}
                                    <br>
                                    @endif

                                    <hr class="hr-line-dashed">
                                </div>
                                <div class="col-lg-12">
                                    @if($trans['block_count'])
                                    <h2 class="no-margins">{{ $trans['current_count'] }} / {{ $trans['limit_count_exceed'] }}</h2>
                                    <div class="stat-percent font-bold text-{{ ($trans['limit_count_usage'] >= 100)? 'danger' : 'info' }}">{{ $trans['limit_count_usage'] }} %</div> {{trans('localize.total_transaction_number')}}
                                    @else
                                    <h2 class="no-margins">{{ $trans['current_count'] }}</h2> {{trans('localize.total_transaction_number')}}
                                    <br>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th class="text-center">{{trans('localize.transaction')}} {{trans('localize.limit')}}</th>
                                    <th class="text-center">{{trans('localize.action')}}</th>
                                    <th class="text-center">{{trans('localize.amount')}}</th>
                                    <th class="text-center">{{trans('localize.number_of_transaction')}}</th>
                                    <th class="text-center">{{trans('localize.per_user')}}</th>
                                    <th class="text-center">{{trans('localize.action')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                <form action="{{ url('admin/store/limit-action/create', [$store->stor_merchant_id, $store->stor_id, $store->limit->id]) }}" method="POST" id="create-action">
                                {{ csrf_field() }}
                                <tr class="text-center">
                                    <td class="text-center">
                                        <select name="type" id="type" class="form-control text-center compulsary create" id="type">
                                            <option value="">-- {{trans('localize.Select_Transaction_Limit')}} --</option>
                                            @foreach($types as $type_id => $type)
                                                <option value="{{ $type_id }}">{{ $type }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select name="action" id="action" class="form-control text-center compulsary create">
                                            <option value="">-- {{trans('localize.Select_Trigger_Action')}} --</option>
                                            @foreach($actions as $action_id => $action)
                                                <option value="{{ $action_id }}">{{ $action }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="amount" id="amt" class="form-control text-center number compulsary create" value="">
                                    </td>
                                    <td>
                                        <span id="number_transaction"> - </span>
                                        <input type="hidden" name="number_transaction" id="nmber" class="form-control number text-center create" value="">
                                    </td>
                                    <td>
                                        <div id="user_block" style="display:none;">
                                            <input type="checkbox" class="form-control" name="per_user" value="1">
                                        </div>
                                    </td>
                                    <td>
									@if($add)
                                        <button type="button" data-action="create-action-btn" class="btn btn-outline btn-block btn-primary"> {{trans('localize.add')}} </button>
									@endif
									</td>
                                </tr>
                                </form>

                                @foreach($store->limit->actions as $action)
                                    <tr id="action-{{$action->id}}" class="text-center">
                                        <td class="text-center">
                                            {{ $types[$action->type] }}
                                        </td>
                                        <td>
                                            <label class="label label-{{ $action->action == 1? 'success' : 'warning' }}">{{ $actions[$action->action] }}</label>
                                        </td>
                                        <td>
                                            @if($action->amount)
                                            <small>{{ $store->country? $store->country->co_curcode : '' }}</small> {{ number_format($action->amount, 2) }}
                                            @else
                                            -
                                            @endif
                                        </td>
                                        <td>
                                            {{ ($action->number_transaction)? $action->number_transaction : '-'}}
                                        </td>
                                        <td>
                                            @if($action->per_user == 1)
                                                <span class="text-success"> <i class="fa fa-check fa-2x"></i> </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-block">
												@if($edit)
                                                <button class="btn btn-outline btn-primary" type="button" style="width:50%;" data-action="edit-store-action" url="{{ url('admin/store/limit-action/edit', [$store->stor_merchant_id, $store->stor_id, $action->id]) }}"><i class="fa fa-pencil"></i> {{trans('localize.edit')}}</button>
												@endif
												@if($delete)
                                                <button class="btn btn-outline btn-danger" type="button" style="width:50%;" data-action="delete-store-action" url="{{ url('admin/store/limit-action/delete', [$store->stor_merchant_id, $store->stor_id, $action->id]) }}"><i class="fa fa-trash"></i> {{trans('localize.delete')}}</button>
												@endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script>
$(document).ready(function() {

    $('.i-checks').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
    });

    $('#type').change(function() {
        if($(this).val() != 1) {
            $('#number_transaction').hide();
            $("input[name='number_transaction']").attr('type', 'number');
            $("input[name='amount']").removeClass("compulsary");
        } else {
            $('#number_transaction').show();
            $("input[name='number_transaction']").attr('type', 'hidden');
            $("input[name='amount']").addClass("compulsary");
        }
    });

    $('#action').change(function() {
        if($(this).val() == 1) {
            $("#user_block").hide();
        } else {
            $("#user_block").show();
        }
    });

    $('button').on('click', function() {
        var this_action = $(this).attr('data-action');

        if (this_action == 'edit-store-action') {
            var url = $(this).attr('url');
            $.get( url, function( data ) {
                $('#myModal-static').modal();
                $('#myModal-static').on('shown.bs.modal', function(){
                    $('#myModal-static .load_modal').html(data);
                });
                $('#myModal-static').on('hidden.bs.modal', function(){
                    $('#myModal-static .modal-body').data('');
                });
            });
        } else if (this_action == 'delete-store-action') {
            var url = $(this).attr('url');

            swal({
                title: window.translations.sure,
                text: "{{trans('localize.Sure_to_delete_this_data')}}",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d9534f",
                confirmButtonText: window.translations.yes,
                cancelButtonText: window.translations.cancel,
                closeOnConfirm: true
            }, function(){
                $('#spinner').show();
                window.location.href = url;
            });
        } else if(this_action == 'create-action-btn') {
            var valid = true;

            $(".create").each(function(){
                if ($(this).hasClass('compulsary')) {
                    if (!$(this).val()) {
                        $(this).css('border', '1px solid red').focus();
                        swal("@lang('localize.fieldrequired')", '', 'error');
                        valid = false;
                        return false;
                    }
                }

                if ($(this).hasClass('number')) {
                    if ($(this).val() && $(this).val() < 1) {
                        $(this).css('border', '1px solid red').focus();
                        swal("@lang('localize.min_value', ['value' => '1'])", '', 'error');
                        valid = false;
                        return false;
                    }
                }

                $(this).css('border', '');
            });

            if(valid) {
                if(!$("#amt").val() && !$("#nmber").val()) {
                    $("#amt").css('border', '1px solid red').focus();
                    $("#nmber").css('border', '1px solid red').focus();
                    swal("Please insert either amount or number of transaction", '', 'error');
                    return false;
                }

                $("#create-action").submit();
            }
        }
    });
});
</script>
@endsection
