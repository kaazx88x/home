@extends('admin.layouts.master')
@section('title', 'Edit Size')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Edit Size</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">Dashboard</a>
            </li>
            <li>
                <a href="/admin/setting/size">Size</a>
            </li>
            <li class="active">
                <strong>Edit</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-lg-6">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>General Info</h5>
                </div>

                <div class="ibox-content">
                    @include('admin.common.success')
                    @include('admin.common.errors')
                    <form class="form-horizontal" action='/admin/setting/size/edit/{{$size->si_id}}' method="POST">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Name</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Name" class="form-control" name='name' required="required" value='{{empty(old('name'))?$size->si_name:old('name')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Status </label>
                                    <div class="col-lg-10">
                                        <div class="i-checks">
                                            <label>
                                                <input type="radio" value="1" name="status" {{(empty(old('status')) && $size->status) || old('status')=='1'?'checked':''}} > <i></i> Active
                                            </label>
                                        </div>
                                        <div class="i-checks">
                                            <label> <input type="radio" value="0" name="status" {{(empty(old('status')) && !$size->status) || old('status')=='0'?'checked':''}}> <i></i> Inactive
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