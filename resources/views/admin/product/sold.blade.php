@extends('admin.layouts.master')

@section('title', 'Sold Out Product')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Sold Out Product</h2>
        <ol class="breadcrumb">
            <li>
                Product
            </li>
            <li class="active">
                <strong>Sold Out</strong>
            </li>
        </ol>
    </div>
</div>

<div class="wrapper wrapper-content animated fadeInRight ecommerce">
    <div class="ibox float-e-margins border-bottom">
        <a class="collapse-link nolinkcolor">
            <div class="ibox-title ibox-title-filter">
                <h5>@lang('localize.Search_Filter')</h5>
                <div class="ibox-tools">
                    <i class="fa fa-chevron-down"></i>
                </div>
            </div>
        </a>
        <div class="ibox-content ibox-content-filter" style="display:none;">
            <div class="row">
                <form class="form-horizontal" id="filter" action='/admin/product/sold' method="GET">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">#ID</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['id']}}" placeholder="Search By Product ID" class="form-control" id="id" name="id">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Product Name</label>
                        <div class="col-sm-9">
                            <input type="text" value="{{$input['name']}}" placeholder="Search By Product Name" class="form-control" id="name" name="name">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Sort By</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sort" name="sort" style="font-family:'FontAwesome', sans-serif;">
                                <option value="id_asc" {{ ($input['sort'] == "id_desc") ? 'selected' : '' }}>#ID : &#xf162;</option>
                                <option value="id_desc" {{ ($input['sort'] == "" || $input['sort'] == "id_desc") ? 'selected' : '' }}>#ID : &#xf163;</option>
                                <option value="name_asc" {{ ($input['sort'] == "name_asc") ? 'selected' : '' }}>Product Name : &#xf15d;</option>
                                <option value="name_desc" {{ ($input['sort'] == "name_desc") ? 'selected' : '' }}>Product Name : &#xf15e;</option>
                                <option value="new" {{($input['sort'] == 'new') ? 'selected' : ''}}>Newest</option>
                                <option value="old" {{($input['sort'] == 'old') ? 'selected' : ''}}>Oldest</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-9 col-sm-offset-2">
                            <button type="submit" class="btn btn-block btn-outline btn-primary" id="filter">Search</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            @include('admin.common.success')
            <div class="ibox">
                <div class="ibox-content">
                    <div class="table-responsive">
                        <table class="table table-bordered table-stripped">
                            <thead>
                                <tr>
                                    <th class="text-center">#ID</th>
                                    <th>Name</th>
                                    <th class="text-center">Retail Price</th>
                                    <th class="text-center">@lang('common.credit_name')</th>
                                    <th class="text-center">Image</th>
                                    <th data-hide="phone" data-sort-ignore="true" class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($solds as $key => $sold)
                                <?php
                                    $mer_id = $sold->pro_mr_id;
                                    $image = env('IMAGE_DIR').'/product/'.$mer_id.'/'.$sold->image;
                                ?>
                                <tr>
                                    <td class="text-center"><a class="nolinkcolor" href="/admin/product/view/{{$sold->pro_mr_id}}/{{$sold->pro_id}}" data-toggle="tooltip" title="View this product">{{$sold->pro_id}}</a></td>
                                    <td>{{$sold->pro_title_en}}</td>
                                    <td class="text-center">{{$sold->pro_price}}</td>
                                    <td class="text-center">{{$sold->pro_vtoken_value}}</td>
                                    <td class="text-center" width="13%"><img alt="image" src="{{$image}}" onerror="if (this.src != 'error.jpg') this.src = '/web/images/stock.png';" class="img-responsive img-thumbnail"></td>
                                    <td class="text-center">
                                        <a class="btn btn-white btn-sm" href="/admin/product/edit/{{$mer_id}}/{{$sold->pro_id}}" >
                                            <span><i class="fa fa-edit"></i> Edit</span>
                                        </a>
                                        <a href="/admin/product/view/{{$mer_id}}/{{$sold->pro_id}}" class="btn btn-white btn-sm">
                                            <span><i class="fa fa-tasks"></i> View</span>
                                        </a>
                                        {{-- <button type="button" class="btn btn-white btn-sm" data-id="{{$sold->pro_id}}" data-post="data-php" data-action="detail"><i class="fa fa-tasks"></i> View Details</button> --}}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <tfoot>
                            <tr>
                                <td colspan="10">
                                    <div class="row">
                                        <div class=" col-xs-6">
                                            <span class="pagination">
                                                Showing {{$solds->firstItem()}} to {{$solds->lastItem()}} of {{$solds->Total()}} Records
                                            </span>
                                        </div>
                                        <div class="col-xs-6 text-right">
                                            <div class="col-xs-7 text-right">
                                                {{$solds->appends(Request::except('page'))->links()}}
                                            </div>
                                            <div class="col-xs-5 text-right pagination">
                                                Go To Page
                                                <input type='number' id='pageno' name='pageno' size="3" min="1" max="{{$solds->lastPage()}}">
                                                <button type="button" class="btn btn-primary btn-sm" onclick="gotopage('page')">
                                                    <i class="fa fa-share-square-o"></i> Go
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                        {{-- <div class="text-center">{{$solds->appends(Request::except('page'))->links()}}</div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    $(document).ready(function() {

        $('button').on('click', function(){
            var this_id = $(this).attr('data-id');
            var this_action = $(this).attr('data-action');
            if (this_action == 'detail'){
                $.get( '/admin/product/detail/' + this_id, function( data ) {
                    $('#myModal').modal();
                    $('#myModal').on('shown.bs.modal', function(){
                        $('#myModal .load_modal').html(data);
                    });
                    $('#myModal').on('hidden.bs.modal', function(){
                        $('#myModal .modal-body').data('');
                    });
                });
            }
        });

        $("#pageno").change(function(){
            var input = $(this).val();
            var max = {{$solds->lastPage()}};
            if(input > max){
                $(this).val(max);
            }
        });

    });

    function gotopage($page) {
            $val =  $("#pageno").val();

            var href = window.location.href.substring(0, window.location.href.indexOf('?'));
            var qs = window.location.href.substring(window.location.href.indexOf('?') + 1, window.location.href.length);
            var newParam = $page + '=' + $val;

            if (qs.indexOf($page + '=') == -1) {
                if (qs == '') {
                    qs = '?'
                }
                else {
                    qs = qs + '&'
                }
                qs = newParam;

            }
            else {
                var start = qs.indexOf($page + "=");
                var end = qs.indexOf("&", start);
                if (end == -1) {
                    end = qs.length;
                }
                var curParam = qs.substring(start, end);
                qs = qs.replace(curParam, newParam);
            }
            window.location.replace(href + '?' + qs);
        }
</script>
@endsection