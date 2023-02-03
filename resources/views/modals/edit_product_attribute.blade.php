<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="row">
        <div class="col-sm-3"><img src="{{ url('/assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:35px;"></div>
        <div class="col-sm-7 text-center" style="vertical-align: middle;"><h3>@lang('localize.edit_attribute')</h3></div>
    </div>
</div>

<div class="modal-body">
    <div class="row">
        <form class="form-horizontal" action="{{ url('update_attribute_parent', $pro_id) }}" method="POST" enctype="multipart/form-data" id="attribute_form">
        {{ csrf_field() }}
        <div class="table-responsive col-sm-12">
            <table class="table table-striped" id="attribute_table">
                <tbody>
                    <tr>
                        <td style="width:70%;">
                            <input type="text" class="form-control" name="attribute" id="attribute" value="{{ $lists['attribute_name_en'] }}" placeholder="Attribute (English)">
                            <input type="hidden" name="old_attribute" value="{{ $lists['attribute_name_en'] }}">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:70%;">
                            <input type="text" class="form-control" name="attribute_cn" id="attribute_cn" value="{{ $lists['attribute_name_cn'] }}" placeholder="Attribute (Chinese Simplified)">
                            <input type="hidden" name="old_attribute_cn" value="{{ $lists['attribute_name_cn'] }}">
                        </td>
                    </tr>
                    <tr>
                        <td style="width:70%;">
                            <input type="text" class="form-control" name="attribute_cnt"  id="attribute_cnt" value="{{ $lists['attribute_name_cnt'] }}" placeholder="Attribute (Chinese Traditional)">
                            <input type="hidden" name="old_attribute_cnt" value="{{ $lists['attribute_name_cnt'] }}">
                        </td>
                        <td><button type="button" class="btn btn-block btn-primary" id="submit_edit">@lang('localize.update')</button></td>
                    </tr>
                    @foreach($lists['items'] as $list)
                    <tr>
                        <td style="width:70%;">
                            <b>@lang('localize.English')</b> : {{ $list->attribute_item }} <br>
                            <b>@lang('localize.Chinese') @lang('localize.Simplified')</b> : {{ $list->attribute_item_cn }}<br>
                            <b>@lang('localize.Chinese') @lang('localize.Traditional')</b> : {{ $list->attribute_item_cnt }}
                        </td>
                        <td class="text-center"><button type="button" class="btn btn-link text-danger delete_item" data-id="{{ $list->id }}"><i class="fa fa-trash"></i> @lang('localize.delete')</button></td>
                    </tr>
                    @endforeach
                    <tr id="input_tr">
                        <label class="col-sm-3">@lang('localize.attribute')</label>
                        <div class="col-sm-6">
                            <td ><input type="text" class="form-control" name="item" placeholder="@lang('localize.attribute_item_en')"></td>
                        </div>
                    </tr>
                    <tr>
                        <td ><input type="text" class="form-control" name="item_cn" placeholder="@lang('localize.attribute_item_cn')"></td>
                    </tr>
                    <tr>
                        <td ><input type="text" class="form-control" name="item_cnt" placeholder="@lang('localize.attribute_item_cnt')"></td>
                    </tr>
                    <tr>
                        <td><button type="button" class="btn btn-outline btn-block btn-primary add_item">@lang('localize.add')</button></td>
                    </tr>
                </tbody>
            </table>
        </div>
        </form>
    </div>
</div>
<div class="modal-footer">
    <a href="" class="btn btn-default" type="button">{{trans('localize.close_')}}</a>
    {{-- <button data-dismiss="modal" class="btn btn-default" type="button">{{trans('localize.close_')}}</button> --}}
</div>

<script>

$(".add_item").click(function(event) {

    $.get( '/add_attribute_item?item=' + $('input[name=item]').val() + '&itemcn='+ $('input[name=item_cn]').val() + '&itemcnt='+ $('input[name=item_cnt]').val() +'&attribute={{ $lists["attribute_name_en"] }}&mer_id={{ $mer_id }}&pro_id={{ $pro_id }}&attribute_cn={{ $lists["attribute_name_cn"] }}&attribute_cnt={{ $lists["attribute_name_cnt"] }}', function( response ) {
        console.log(response);
        if (response['status'] == true) {
            swal("{{ trans('localize.swal_success') }}", "{{ trans('localize.swal_updated') }}", "success");
            $('<tr><td style="width:70%;"> <b>@lang('localize.English')</b> :'+response['item']+'<br><b>@lang('localize.Chinese')</b> :'+response['item_cn']+'<br><b>@lang('localize.Chinese') @lang('localize.Simplified')</b> :'+response['item_cnt']+'</td><td class="text-center"><button type="button" class="btn btn-link text-danger delete_item" data-id="'+response['id']+'"><i class="fa fa-trash"></i> @lang("localize.delete")</button></td></tr>').insertBefore('#input_tr');
            $('input[name=item]').val('');
            $('input[name=item_cn]').val('');
            $('input[name=item_cnt]').val('');
        } else {
            swal("{{ trans('localize.swal_error') }}", response['message'] , "error");
        }
    });
});

$(document).ready(function() {

    $("body").on('click','.delete_item', function(event) {
        attribute_id = $(this).attr('data-id');
        $.get('/attribute/check_attribute_exist/{{ $mer_id }}/{{ $pro_id }}/' + attribute_id, function( response ) {
            if(response == 0) {

                swal({
                    title: "{{ trans('localize.sure') }}",
                    text: "{{ trans('localize.confirm_delete_attribute') }}",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d9534f",
                    confirmButtonText: "{{ trans('localize.yes_delete_it') }}",
                    cancelButtonText: "{{ trans('localize.swal_cancel') }}",
                    closeOnConfirm: true
                }, function(){
                    var url = '/product/attribute/delete/'+attribute_id+'/normal?product_id={{$pro_id}}&merchant_id={{ $mer_id }}';
                    window.location.href = url;
                });

            } else if (response == 1) {

                swal({
                    title: "Warning!",
                    text: "This attributes has link with product pricing, Pricing that related with this attribute <span class='text-danger'>will be deactive and unable to set active until edit pricing attributes, Item in customer cart also will be removed</span>. Continue?",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#d9534f",
                    confirmButtonText: "{{ trans('localize.yes_delete_it') }}",
                    cancelButtonText: "{{ trans('localize.swal_cancel') }}",
                    closeOnConfirm: true,
                    html: true
                }, function(){
                    var url = '/product/attribute/delete/'+attribute_id+'/force?product_id={{$pro_id}}&merchant_id={{ $mer_id }}';
                    window.location.href = url;
                });

                return false;
            }
        });
    });

    $("#submit_edit").click(function(event) {
        if($('#attribute').val() == '') {
            $('#attribute').attr('placeholder', '{{trans('localize.fieldrequired')}}');
            $('#attribute').css('border', '1px solid red');
            return false;
        } else {
            $('#attribute').css('border', '');
        }

        $('#attribute_form').submit();
    });
});
</script>
