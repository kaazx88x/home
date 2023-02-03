<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h4>@lang('localize.statement_reference')</h4>
</div>

<div class="modal-body">
    <div class="row">
        <div class="col-lg-12">
            @if($type == 'pdf')
                <embed src="{{ $file }}" width="100%" height="550px;" />
            @else
                <img src="{{ $file }}" class="img-responsive img-thumbnail">
            @endif
        </div>
    </div>
</div>

<div class="modal-footer">
    <button data-dismiss="modal" class="btn btn-default" type="button">@lang('localize.closebtn')</button>
    <a href="{{ $file }}" class="btn btn-primary" download>@lang('localize.download')</a>
</div>

