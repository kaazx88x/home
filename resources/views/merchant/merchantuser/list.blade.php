@extends('merchant.layouts.master')
@section('title', 'Merchant Users')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Merchant Users</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
             @include('merchant.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        <a href="/merchant/merchant-user/create" class="btn btn-primary btn-xs">Create new merchant</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <input type="text" class="form-control input-sm m-b-xs" id="filter"
                           placeholder="Search in table">

                    <table class="footable table table-stripped" data-page-size="20" data-filter=#filter>
                      <thead>
                          <tr>
                              <th>Name</th>
                              <th>Email</th>
                              <th>Username</th>
                              <th data-hide="phone">Status</th>
                              <th data-hide="phone">Last Login Date</th>
                              <th data-hide="phone" data-sort-ignore="true">Last Login IP</th>
                              <th data-hide="phone" data-sort-ignore="true">Action</th>
                          </tr>
                      </thead>
                        <tbody>
                            @foreach($merchantusers as $merchantuser)
                            <tr>
                                <td>{{$merchantuser->name}}</td>
                                <td>{{$merchantuser->email}}</td>
                                <td>{{$merchantuser->username}}</td>
                                <td><span class="label label-{{$merchantuser->status?'primary':'default'}}">{{$merchantuser->status?'Active':'Inactive'}}</span></td>
                                <td>{{$merchantuser->last_login_date}}</td>
                                <td>{{$merchantuser->last_login_ip}}</td>
                                <td><a href="/merchant/merchant-user/edit/{{$merchantuser->id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i> Edit </a></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                                <tr>
                                    <td colspan="7">
                                        <ul class="pagination pull-right"></ul>
                                    </td>
                                </tr>
                                </tfoot>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>

<script>
    $(document).ready(function() {

        $('.footable').footable();

    });
</script>
@endsection
