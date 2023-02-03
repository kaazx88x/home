@extends('admin.layouts.master')
@section('title', 'Create Color')

@section('content')
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-sm-4">
        <h2>Create New Color</h2>
        <ol class="breadcrumb">
            <li>
                <a href="/admin">Dashboard</a>
            </li>
            <li>
                <a href="/admin/setting/color">Color</a>
            </li>
            <li class="active">
                <strong>Create</strong>
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
                    @include('admin.common.errors')
                    <form class="form-horizontal" action='/admin/setting/color/add' method="POST">
                        {{ csrf_field() }}
                        <div id="ntc">
                            <div id="picker">
                                <div class="farbtastic">
                                    <div class="color" style="background-color: rgb(0, 255, 19);"></div>
                                    <div class="wheel"></div>
                                    <div class="overlay"></div>
                                    <div class="h-marker marker" style="left: 166px; top: 145px;"></div>
                                    <div class="sl-marker marker" style="left: 81px; top: 98px;"></div>
                                </div>
                            </div>
                        </div>

                        <div id="colortag">
                            <h2 id="colorname" style="min-width:300px">Apple<sup>approx.</sup></h2>
                            <input type="hidden" id="color_name" name="color_name"  class="form-control" readonly />
                            <div id="colorpick">
                                <select id="colorop" class="form-control">
                                    <option value="">Select a Color:</option>
                                </select>
                            </div>
                            <div style="background-color: rgb(42, 207, 54);" id="colorbox">
                                <div style="background-color: rgb(79, 168, 61);" id="colorsolid"></div>
                            </div>
                            <div id="colorpanel">
                                <div id="colorhex">Your Color:</div>
                                <input type="text" maxlength="10" value="" class="inputbox" id="colorinp" name="color">
                                <div id="colorrgb">RGB: 42, 207, 54</div>
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
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('style')
<link href="/backend/css/plugins/ntc/farbtastic.css" rel="stylesheet">
@endsection

@section('script')
<script src="/backend/js/plugins/ntc/farbtastic.js"></script>
<script src="/backend/js/plugins/ntc/ntc.js"></script>
<script src="/backend/js/plugins/ntc/ntc_main.js"></script>

<script>
    $(document).ready(function() {
        $('#picker').farbtastic(function(e){});
        //var n_match = ntc.name("#6195ED");
        //n_rgb = n_match[0]; // RGB value of closest match
        //n_name = n_match[1]; // Text string: Color name
        //n_exactmatch = n_match[2]; // True if exact color match

        //alert(n_name);
    });
</script>
@endsection