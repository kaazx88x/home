@extends('admin.layouts.master')
@section('title', 'Manage Category Filters')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Manage_Category_Filters')}}</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('admin.common.success')
            @include('admin.common.errors')
            <div class="ibox">
                {{-- <div class="ibox-title">
                    <div class="ibox-tools">
                        <a class="btn btn-primary btn-xs">{{trans('localize.Create_New_Category')}}</a>
                    </div>
                </div> --}}
                <div class="ibox-content">
                    <div class="col-sm-12 text-center">
                        <h2>{{trans('localize.Filters_for')}} {{ $category->name_en }}</h2>
                        <hr>
                    </div>

                    <select class="form-control dual_select" multiple="false" style="display: none;">
                        @foreach ($filters as $filter)
                            <option value="{{$filter->id}}" {{ ($filter->selected == 1)? 'selected' : '' }}>{{ $filter->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/dualListbox/bootstrap-duallistbox.min.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/backend/js/plugins/dualListbox/jquery.bootstrap-duallistbox.js"></script>

<script>
    $(document).ready(function(){

        var filter_id;
        var selectOption;
        var category_id = "{{ $category->id }}";
        var box = $('.dual_select').bootstrapDualListbox({
            selectorMinimalHeight: 400,
            nonSelectedListLabel: '{{trans('localize.Non_selected')}}',
            selectedListLabel: '{{trans('localize.Selected')}}',
            moveOnSelect: false,
            moveSelectedLabel: '{{trans('localize.Select_this_filter')}}',
            moveAllLabel: '{{trans('localize.Select_all_filter')}}',
            removeSelectedLabel: '{{trans('localize.Remove_selected_filter')}}',
            removeAllLabel: '{{trans('localize.Remove_all_selected_filter')}}',
            preserveSelectionOnMove: 'moved',
        });

        $('select[name="_helper1"]').parent().find('.moveall').hide();
        $('select[name="_helper2"]').parent().find('.removeall').hide();
        $('select[name="_helper1"]').parent().find('.move').attr('style', 'width: 100%');
        $('select[name="_helper2"]').parent().find('.remove').attr('style', 'width: 100%');

        $('#bootstrap-duallistbox-nonselected-list_').on('click',function() {
            filter_id = this.value;
        });

        $('#bootstrap-duallistbox-selected-list_').on('click',function(){
            filter_id = this.value;
        });

        $('.move').on('click', function() {
            if(filter_id) {
                $.ajax({
                    type: 'get',
                    data: { filter_id: filter_id, category_id: category_id },
                    url: '/admin/setting/category_filter/add',
                    beforeSend : function() {
                        $('#spinner').show();
                    },
                    success: function () {
                        $('#spinner').hide();
                    }
                });
            } else {
                swal('Warning!','Please select filter before click arrow button','warning')
            }


        });

        $('.remove').on('click',function() {

            $.ajax({
                type: 'get',
                data: { filter_id: filter_id, category_id: category_id },
                url: '/admin/setting/category_filter/remove',
                beforeSend : function() {
                    $('#spinner').show();
                },
                success: function () {
                    $('#spinner').hide();
                }
            });

        });
    });

    function changeFunc(value)
    {
        alert(value);
    }
</script>
@endsection