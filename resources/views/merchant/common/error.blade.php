@if (!empty($error) || session('error'))
    <!-- Form Error List -->
    <div class="alert alert-danger alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        {{ !empty($error)?$error: session('error')}}
    </div>
@endif