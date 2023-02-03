@extends('merchant.layouts.master')

@section('title', 'Store Limit')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">


    <div class="col-sm-4">
        <h2>@lang('localize.store_limit')</h2>
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
                            <span class="label label-{{ ($trans['block_amount'] || $trans['block_count'])? 'warning' : 'success' }} pull-right">{{ ($trans['block_amount'] || $trans['block_count'])? trans('localize.limited') : trans('localize.unlimited') }}</span>
                        </div>
                        <div class="ibox-content">
                            <div class="row">
                                <div class="col-lg-12">
                                    @if($trans['block_amount'])
                                    <h2 class="no-margins"><small>{{ $store->country? $store->country->co_curcode : '' }}</small> {{ number_format($trans['current_amount'], 2) }} / {{ number_format($trans['limit_amount_exceed'], 2) }}</h2>
                                    <div class="stat-percent font-bold text-{{ ($trans['limit_amount_usage'] >= 100)? 'danger' : 'info' }}">{{ $trans['limit_amount_usage'] }} %</div> @lang('localize.total_transaction')
                                    @else
                                    <h2 class="no-margins"><small>{{ $store->country? $store->country->co_curcode : '' }}</small> {{ number_format($trans['current_amount'], 2) }}</h2> @lang('localize.total_transaction')
                                    <br>
                                    @endif

                                    <hr class="hr-line-dashed">
                                </div>
                                <div class="col-lg-12">
                                    @if($trans['block_count'])
                                    <h2 class="no-margins">{{ $trans['current_count'] }} / {{ $trans['limit_count_exceed'] }}</h2>
                                    <div class="stat-percent font-bold text-{{ ($trans['limit_count_usage'] >= 100)? 'danger' : 'info' }}">{{ $trans['limit_count_usage'] }} %</div> @lang('localize.total_transaction_number')
                                    @else
                                    <h2 class="no-margins">{{ $trans['current_count'] }}</h2> @lang('localize.total_transaction_number')
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
                                    <th class="text-center">@lang('localize.transaction_limit')</th>
                                    <th class="text-center">@lang('localize.action')</th>
                                    <th class="text-center">@lang('localize.amount')</th>
                                    <th class="text-center">@lang('localize.number_of_transaction')</th>
                                    <th class="text-center">@lang('localize.per_user')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(!$store->limit->actions->isEmpty())
                                @foreach($store->limit->actions as $action)
                                    <tr class="text-center">
                                        <td class="text-center">
                                            {{ $types[$action->type] }}
                                        </td>
                                        <td>
                                            <label class="label label-{{ $action->action == 1? 'success' : 'warning' }}">{{ $actions[$action->action] }}</label>
                                        </td>
                                        <td>
                                            <small>{{ $store->country? $store->country->co_curcode : '' }}</small> {{ number_format($action->amount, 2) }}
                                        </td>
                                        <td>
                                            {{ ($action->number_transaction)? $action->number_transaction : '-'}}
                                        </td>
                                        <td>
                                            @if($action->per_user == 1)
                                                <span class="text-success"> <i class="fa fa-check fa-2x"></i> </span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                @else
                                    <tr class="text-center">
                                        <td class="text-left" colspan="4">
                                            @lang('localize.table_no_record')
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{--
@section('style')
<link href="/backend/css/plugins/iCheck/custom.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/iCheck/icheck.min.js"></script>
<script>
$(document).ready(function() {
    $('#type').change(function() {
        if($(this).val() != 1) {
            $('#number_transaction').hide();
            $("input[name='number_transaction']").attr('type', 'number');
        } else {
            $('#number_transaction').show();
            $("input[name='number_transaction']").attr('type', 'hidden');
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
                text: "Sure to delete this data?",
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
        }
    });

    $("#create-action").submit( function(form) {

        var type = $('#type').val();
        var action = $('#action').val();

        $(':input').each(function(e) {
            if ($(this).hasClass('compulsary')) {
                if (!$(this).val()) {
                    $(this).css('border', '1px solid red').focus();
                    swal("@lang('localize.fieldrequired')", '', 'error');
                    form.preventDefault();
                    return false;
                }

                if($(this).hasClass('amount') && $(this).val() < 10) {
                    $(this).css('border', '1px solid red').focus();
                    swal("{{trans('localize.Amount_must_be_at_least_10')}}", '', 'error');
                    form.preventDefault();
                    return false;
                }
            }
        });
    });
});
</script>
@endsection
--}}