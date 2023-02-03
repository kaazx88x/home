@extends('admin.layouts.master')
@section('title', 'Manage Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Manage Publish Blog</h2>
    </div>
</div>

<div class="row">
    <div class="col-lg-12">
        <div class="wrapper wrapper-content animated fadeInUp">
            @include('admin.common.success')
            <div class="ibox">
                <div class="ibox-title">
                    <div class="ibox-tools">
                        @if (session('status'))
                            <br>
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        <a href="/admin/customer/add" class="btn btn-primary btn-xs">Create new Customer</a>
                    </div>
                </div>
                <div class="ibox-content">
                    <input type="text" class="form-control input-sm m-b-xs" id="filter" placeholder="Search in table">
                    <div class="table-responsive">
                        <table class="footable table table-stripped" data-page-size="20" data-filter=#filter>
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th data-hide="phone">Name</th>
                                    <th data-hide="phone" data-sort-ignore="true">email</th>
                                    <th>Joined Date</th>
                                    <th>VCoin</th>
                                    <th>Game Point</th>
                                    <th>Edit</th>
                                    <th>Status</th>
                                    <th>Delete</th>
                                    <th>Login Type</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach($blogs as $blog)
                                <tr>
                                    <td>{{ $i }}</td>
                                    <td>{{$blog->blog_tittle}}</td>
                                    <td>{{$blog->blog_desc}}</td>
                                    <td>{{$blog->blog_created_date}}</td>
                                    <td style="text-align:center;">{{$blog->image}}</td>
                                    <td style="text-align:center;">{{$customer->game_point}}</td>
                                    <td><a href="/admin/customer/edit/{{$customer->cus_id}}" class="btn btn-white btn-sm"><i class="fa fa-pencil"></i>Edit</a></td>
                                    <?php if($customer->cus_status==0){ ?>
                                        <td style="text-align:center;"><a href="/admin/customer/block/{{$customer->cus_id}}/blocked"><i class='fa fa-check fa-2x'></i></a></td>
                                    <?php } else { ?>
                                        <td style="text-align:center;"><a href="/admin/customer/block/{{$customer->cus_id}}/unblocked"><i class='fa fa-ban fa-2x'></i></a></td>
                                    <?php } ?>
                                    <td style="text-align:center;"><a href="/admin/customer/delete/{{$customer->cus_id}}" class="fa fa-trash fa-2x"><i></i></a></td>
                                    <td>{{$logintype}}</td>

                                </tr>
                                <?php $i++; ?>
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
                        <div class="text-center"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/footable/footable.core.css" rel="stylesheet">
<link href="/backend/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/footable/footable.all.min.js"></script>
<script src="/backend/js/plugins/sweetalert/sweetalert.min.js"></script>

<script>
    $(document).ready(function() {

        //$('.footable').footable();

    });
</script>
@endsection
