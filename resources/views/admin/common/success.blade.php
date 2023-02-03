@if (!empty($success) || session('success'))
    <!-- Form Error List -->
    <div class="alert alert-success alert-dismissable">
        <button aria-hidden="true" data-dismiss="alert" class="close" type="button">×</button>
        {{ !empty($success)?$success: session('success')}}
    </div>
@endif