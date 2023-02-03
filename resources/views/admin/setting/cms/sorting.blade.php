<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
    <div class="row">
        <div class="col-sm-3"><img src="{{ url('assets/images/meihome_logo.png') }}" alt="" style="width:auto; height:55px;"></div>
        <div class="col-sm-7 text-center"><h2>{{trans('localize.Footer_Sorting')}}</h2></div>
    </div>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-lg-12 text-center">
            <p><label class="">{{trans('localize.Drag_and_drop_list_to_sort_footer_link')}}</label></p>
        </div>
        <div class="col-lg-12">
            <div class="table-responsive">
                <table class="table table-stripped table-hover">
                    <thead>
                        <tr>
                            <th class="text-center">{{trans('localize.Name')}}</th>
                        </tr>
                    </thead>
                    <tbody class="sort">
                        @foreach($footers as $footer)
                        <tr id="{{$footer->cp_id}}" style="cursor: pointer; cursor: hand;" class="text-center">
                            <td>{{$footer->title}}</td>
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
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">{{trans('localize.closebtn')}}</button>
</div>
<link href="/backend/css/plugins/sortable/sortable.css" rel="stylesheet">
<script src="/backend/js/plugins/sortable/sortable.js"></script>
<script>
    $(document).ready(function() {

        $( ".sort" ).sortable({
            update : function () {
                var order = $(this).sortable('toArray').toString();
                var pass = 'newOrder='+order;

                 $.ajax({
                    type: 'get',
                    data: pass,
                    url: '/admin/setting/cms/footer/update_footer_sorting',
                    beforeSend : function() {
                        $('#spinner').show();
                    },
                    success: function () {
                        $('#spinner').hide();
                    }
                });
            }
        });

    });
</script>