@if (!empty($success) || session('error'))
<!-- Form Error List -->
<div class="alert alert-danger alert-dismissable">
    <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
    {{ !empty($success)?$success: session('error')}}
</div>
@endif