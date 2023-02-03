@extends('layouts.web.master')

@section('content')
	<div class="page-title">
				<a href="/" class="back transition"><i class="fa fa-angle-left"></i></a>
				{{ $cms->cp_title_en }}
			</div>
			<div class="ContentWrapper">
				<div class="panel-general">
					<div class="form-general">
						<div class="panel-title">{{ $cms->cp_title_en }}</div>
						{!! $cms->cp_description_en !!}
					</div>

				</div>

			</div>
	</div>
@endsection
