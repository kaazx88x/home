@extends('admin.layouts.master')
@section('title', 'Edit Attribute Item')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>{{trans('localize.Edit')}} {{trans('localize.Filter_Item')}}</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin/setting/filter">{{trans('localize.Filter')}}</a>
            </li>
            <li class="active">
                <a href="/admin/setting/filter/item/{{ $filter->parent_id }}">{{$filter->parent_name}}</a>
            </li>
            <li>
                {{$filter->name}}
            </li>
            <li class="active">
                <strong>{{trans('localize.Edit')}}</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>{{trans('localize.General_Info')}}</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.errors')
                    <form id='form' class="form-horizontal" action='/admin/setting/filter/item/edit/{{$filter->id}}' method="POST">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Name')}}</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="{{trans('localize.Name')}}" class="form-control compulsary" name='name'  value='{{ $filter->name }}'>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-lg-2 control-label">{{trans('localize.Status')}}</label>
                            <div class="col-lg-10">
                                <div class="i-checks">
                                    <label>
                                        <input type="radio" value="1" name="status" {{ ($filter->status == 1)?'checked':''}} > <i></i> {{trans('localize.Active')}}
                                    </label>
                                </div>
                                <div class="i-checks">
                                    <label> <input type="radio" value="0" name="status" {{ ($filter->status == 0)?'checked':''}}> <i></i> {{trans('localize.Inactive')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                @if($edit_permission)
                                <button class="btn btn-sm btn-primary form_submit" type="button">{{trans('localize.Update')}}</button>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {

		$('.form_submit').click(function() {
                var isValid = true;

                $(':input').each(function(e) {
                    if ($(this).hasClass('compulsary')) {
                        if (!$(this).val()) {
                            $(this).attr('placeholder', '{{trans('localize.fieldrequired')}}').css('border', '1px solid red').focus();
                            isValid = false;
                            return false;
                        }
                    }

                    $(this).css('border', '');
                });

                if (isValid) {
                    $("#form").submit();
                }
            });
    });
</script>
@endsection