@if (!empty($error) || session('error'))
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        {{ !empty($error)?$error: session('error')}}
    </div>
@endif

@if (count($errors) > 0)
    <div class="alert alert-danger">
        <strong>{{trans('localize.whoops!')}}</strong><br>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (!empty($success) || session('success'))
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        {{ !empty($success)?$success: session('success')}}
    </div>
@endif

@if (!empty($error_) || session('error_'))
    <!-- Form Error List -->
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        {!! !empty($error_)?$error_: session('error_') !!}
    </div>
@endif