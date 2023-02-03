@if (!empty($success) || session('success'))
<!-- Form Error List -->
<div class="alert alert-success alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {{ !empty($success)?$success: session('success')}}
</div>
@endif

@if (!empty($status) || session('status'))
<!-- Form Error List -->
<div class="alert alert-success alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {!! !empty($status)?$status: session('status') !!}
</div>
@endif

@if (count($errors) > 0)
<!-- Form Error List -->
<div class="alert alert-danger alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    <ul>
        @foreach ($errors->all() as $e)
            <li>{{ $e }}</li>
        @endforeach
    </ul>
</div>
@endif

@if (!empty($error) || session('error'))
<!-- Form Error List -->
<div class="alert alert-danger alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {{ !empty($error)?$error: session('error')}}
</div>
@endif

@if (!empty($error_) || session('error_'))
    <!-- Form Error List -->
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        {!! !empty($error_)?$error_: session('error_') !!}
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning">
        {{ session('warning') }}
    </div>
@endif