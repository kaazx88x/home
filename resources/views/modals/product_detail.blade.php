<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">{{trans('localize.close_')}}</span></button>
    <h4 class="modal-title">{{trans('localize.pro_details')}}</h4>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-5">


            <div class="product-images">

                <div>
                    <div class="image-imitation">
                        [IMAGE 1]
                    </div>
                </div>
                <div>
                    <div class="image-imitation">
                        [IMAGE 2]
                    </div>
                </div>
                <div>
                    <div class="image-imitation">
                        [IMAGE 3]
                    </div>
                </div>


            </div>

        </div>
        <div class="col-md-7">

            <h2 class="font-bold m-b-xs">
                {{$product['details']->pro_title_en}}
            </h2>
            <!--<small>Many desktop publishing packages and web page editors now.</small>-->
            <div class="m-t-md">
                <h2 class="product-main-price">Mei Point : {{$product['details']->pro_vtoken_value}} <small class="text-muted">{{trans('localize.retail')}} : ${{$product['details']->pro_price}}</small> </h2>
            </div>
            <hr>

            <h4>{{trans('localize.product_description')}}</h4>

            <div class="small text-muted">
                {!!$product['details']->pro_desc_en!!}
            </div>
            <dl class="dl-horizontal m-t-md small">
                <dt>{{trans('localize.category')}}</dt>
                <dd>Mobile</dd>
                <dt>{{trans('localize.product_color')}}</dt>
                <dd>Black | Green | Blue</dd>
                <dt>{{trans('localize.product_size')}}</dt>
                <dd>Small | Medium | Big</dd>
                <dt>{{trans('localize.delivery_within')}}</dt>
                <dd>{{$product['details']->pro_delivery}}</dd>
            </dl>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-white btn-xs" data-dismiss="modal">{{trans('localize.close_')}}</button>
</div>
<link href="/backend/css/plugins/slick/slick.css" rel="stylesheet">
<link href="/backend/css/plugins/slick/slick-theme.css" rel="stylesheet">
<script src="/backend/js/plugins/pace/pace.min.js"></script>
<script src="/backend/js/plugins/slick/slick.min.js"></script>
<script>
    $(document).ready(function() {

        $('.product-images').slick({
            dots: true
        });
    });
</script>