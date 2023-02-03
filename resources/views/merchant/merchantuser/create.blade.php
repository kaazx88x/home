@extends('merchant.layouts.master')
@section('title', 'Create Merchant')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create New Merchat User</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/merchant">Dashboard</a>
            </li>
            <li>
                <a href="/merchant/merchant-user">Merchant Users</a>
            </li>
            <li class="active">
                <strong>Create</strong>
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
                    @include('merchant.common.errors')
                    <form class="form-horizontal" action='/merchant/merchant-user/create' method="POST">
                         {{ csrf_field() }}
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Name</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Name" class="form-control" name='name' required="required" value='{{old('name')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Email</label>
                            <div class="col-lg-10">
                                <input type="email" placeholder="Email" class="form-control" name='email' required="required" value='{{old('email')}}'>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Username</label>
                            <div class="col-lg-10">
                                <input type="text" placeholder="Username" class="form-control" name='username' required="required" value='{{old('username')}}'>
                                <span class="help-block m-b-none">alphanumeric only, maximum 100 length</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Status </label>
                                    <div class="col-lg-10">
                                        <div class="i-checks">
                                            <label>
                                                <input type="radio" value="1" name="status" {{old('status')=='1' || empty(old('status'))?'checked':''}} > <i></i> Active
                                            </label>
                                        </div>
                                        <div class="i-checks">
                                            <label> <input type="radio" value="0" name="status" {{old('status')=='0'?'checked':''}}> <i></i> Inactive
                                            </label>
                                        </div>
                                    </div>
                                </div>
                        <div class="form-group">
                            <div class="col-lg-offset-2 col-lg-10">
                                <button class="btn btn-sm btn-primary" type="submit">Create</button>
                                <span class="help-block m-b-none">Email will be sent to this user for creating password</span>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection