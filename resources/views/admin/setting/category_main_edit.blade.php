@extends('admin.layouts.master')
@section('title', 'Edit Main Category')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Edit Main Category</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">Dashboard</a>
            </li>
            <li>
                <a href="/admin/setting/category">Category</a>
            </li>
            <li class="active">
                <strong>Edit</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>General Info</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.success')
                    @include('admin.common.errors')
                    <form class="form-horizontal" action='/admin/setting/category/main/edit/{{$maincategory->smc_id}}' method="POST">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Name (English)</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Name (English)" class="form-control" name='name_en' required="required" value='{{empty(old('name_en'))?$maincategory->smc_name_en:old('name_en')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Name (Chinese)</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Name (Chinese)" class="form-control" name='name_cn' value='{{empty(old('name_cn'))?$maincategory->smc_name_cn:old('name_cn')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Name (Malay)</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Name (Malay)" class="form-control" name='name_my' value='{{empty(old('name_my'))?$maincategory->smc_name_my:old('name_my')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Status </label>
                                    <div class="col-lg-10">
                                        <div class="i-checks">
                                            <label>
                                                <input type="radio" value="1" name="status" {{(empty(old('status')) && $maincategory->smc_status) || old('status')=='1'?'checked':''}} > <i></i> Active
                                            </label>
                                        </div>
                                        <div class="i-checks">
                                            <label> <input type="radio" value="0" name="status" {{(empty(old('status')) && !$maincategory->smc_status) || old('status')=='0'?'checked':''}}> <i></i> Inactive
                                            </label>
                                        </div>
                                    </div>
                                </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-sm btn-primary" type="submit">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection