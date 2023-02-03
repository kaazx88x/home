 <ul class="nav nav-tabs">
    <li class="{{ $link == 'edit'? 'active' : '' }}"><a href="{{ url('admin/product/edit', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}">@lang('localize.Edit') @lang('localize.product')</a></li>
    <li class="{{ $link == 'description'? 'active' : '' }}"><a href="{{ url('admin/product/description', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}">@lang('localize.manage') @lang('localize.description')</a></li>
    <li class="{{ $link == 'image'? 'active' : '' }}"><a href="{{ url('admin/product/image/view', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}">@lang('localize.manage') @lang('localize.image')</a></li>

    @if($product['details']->pro_type <> 4)
    <li class="{{ $link == 'filter'? 'active' : '' }}"><a href="{{ url('admin/product/filter', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}">@lang('localize.manage') @lang('localize.filter')</a></li>
    <li class="{{ $link == 'attribute'? 'active' : '' }}"><a href="{{ url('admin/product/attribute', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}">@lang('localize.manage') @lang('localize.attributes')</a></li>
    @endif

    <li class="{{ $link == 'pricing'? 'active' : '' }}"><a href="{{ url('admin/product/pricing', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}">@lang('localize.manage') @lang('localize.pricing')</a></li>

    @if($product['details']->pro_type == 4)
    <li class="{{ $link == 'ecard'? 'active' : '' }}"><a href="{{ url('admin/product/code/listing', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}">@lang('localize.e-card.serial_number')</a></li>
    @endif

    <li class="{{ $link == 'log'? 'active' : '' }}"><a href="{{ url('admin/product/quantity', [$product['details']->pro_mr_id, $product['details']->pro_id]) }}">@lang('localize.quantity_log')</a></li>
</ul>