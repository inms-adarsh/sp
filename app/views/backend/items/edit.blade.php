@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
    @if ($item->id)
        @lang('admin/items/general.update') ::
    @else
        @lang('admin/items/general.create') ::
    @endif
@parent
@stop

{{-- Page content --}}
@section('content')


<div class="row header">
    <div class="col-md-12">
        <a href="{{ URL::previous() }}" class="btn-flat gray pull-right"><i class="fa fa-arrow-left icon-white"></i> @lang('general.back')</a>
        <h3>
        @if ($item->id)
            @lang('admin/items/general.update')
        @else
            @lang('admin/items/general.create')
        @endif
</h3>
    </div>
</div>

<div class="user-profile">
<div class="row profile">
<div class="col-md-9 bio">

                        <form class="form-horizontal" method="post" action="" autocomplete="off">
                        <!-- CSRF Token -->
                        <input type="hidden" name="_token" value="{{ csrf_token() }}" />

                        <!-- Name -->
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
	                        <div class="col-md-3">
	                        	{{ Form::label('name', Lang::get('admin/items/general.item_name')) }}
	                        	<i class='fa fa-asterisk'></i>
	                        </div>                        
                            <div class="col-md-9">
                                <input class="form-control" type="text" name="name" id="name" value="{{{ Input::old('name', $item->name) }}}" />
                                {{ $errors->first('name', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                            </div>
                        </div>
                        
                        <!-- Type -->
			            <div class="form-group {{ $errors->has('item_type') ? ' has-error' : '' }}">
				            <div class="col-md-3">
			               	{{ Form::label('item_type', Lang::get('general.type')) }}
			               	<i class='fa fa-asterisk'></i>
				            </div>
			                <div class="col-md-7">				                
			                    {{ Form::select('item_type', $item_types , Input::old('item_type', $item->item_type), array('class'=>'select2', 'style'=>'min-width:350px')) }}
			                    {{ $errors->first('item_type', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
			                </div>
			            </div>
                        


						<hr>
                        <!-- Form actions -->
                        <div class="form-group">
                       
                            <div class="col-md-7 col-md-offset-3">
                                <a class="btn btn-link" href="{{ URL::previous() }}">@lang('button.cancel')</a>
                                <button type="submit" class="btn btn-success"><i class="fa fa-check icon-white"></i> @lang('general.save')</button>
                            </div>
                        </div>
                    </form>
                    <br><br><br><br><br>
                    </div>

                    <!-- side address column -->
                    <div class="col-md-3 col-xs-12 address pull-right">
                        <br /><br />
                        <h6>@lang('admin/items/general.about_items')</h6>
                        <p>@lang('admin/items/general.about_items') </p>

                    </div>
</div>
</div>


@stop
