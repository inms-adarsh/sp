@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
    @if ($vehicle->id)
        @lang('admin/vehicles/table.update') ::
    @else
        @lang('admin/vehicles/table.create') ::
    @endif
@parent
@stop

{{-- Page content --}}
@section('content')
<div class="row header">
    <div class="col-md-12">
            <!--<a href="{{ URL::previous() }}" class="btn-flat gray pull-right"><i class="fa fa-arrow-left icon-white"></i>  @lang('general.back')</a>-->
        @if(isset($clone_vehicle))
            <a href="{{{ URL::route('view/vehicle', $clone_vehicle->id) }}}" class="btn-flat gray pull-right"><i class="fa fa-arrow-left icon-white"></i>  @lang('general.back')</a>
        @else
            <a href="{{{ URL::route('view/vehicle', $vehicle->id) }}}" class="btn-flat gray pull-right"><i class="fa fa-arrow-left icon-white"></i>  @lang('general.back')</a>
        @endif
        <h3>
        @if ($vehicle->id)
            @lang('admin/vehicles/table.update')
        @elseif(isset($clone_vehicle))
            @lang('admin/vehicles/table.clone')
        @else
            @lang('admin/vehicles/table.create')
        @endif
        </h3>
    </div>
</div>

<div class="row form-wrapper">


{{ Form::open(['method' => 'POST', 'files' => true, 'class' => 'form-horizontal' ]) }}
    <!-- CSRF Token -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}" />



			
            <!-- Vehicle No. -->
            <div class="form-group {{ $errors->has('vehicleno') ? ' has-error' : '' }}">
                <label for="vehicleno" class="col-md-2 control-label">@lang('general.vehicle_no') 
                <i class='fa fa-asterisk'></i></label>
                    <div class="col-md-7">
                        <input class="form-control" type="text" name="vehicleno" id="vehicleno" value="{{{ Input::old('vehicleno', $vehicle->vehicleno) }}}" />
                        {{ $errors->first('vehicleno', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                    </div>
            </div>

			<!-- Category -->
            <div class="form-group {{ $errors->has('category_id') ? ' has-error' : '' }}">
                <label for="category_id" class="col-md-2 control-label">@lang('general.category')
                 <i class='fa fa-asterisk'></i></label>
                 </label>
                    <div class="col-md-7">
                        {{ Form::select('category_id', $category_list , Input::old('category_id', $vehicle->category_id), array('class'=>'select2', 'style'=>'width:350px')) }}
                        {{ $errors->first('category_id', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                    </div>
            </div>



            <!-- Image -->
            @if ($vehicle->image)
                <div class="form-group {{ $errors->has('image_delete') ? 'has-error' : '' }}">
                    <label class="col-md-2 control-label" for="image_delete">@lang('general.image_delete')</label>
                    <div class="col-md-5">
                        {{ Form::checkbox('image_delete') }}
                        <img src="/uploads/vehicles/{{{ $vehicle->image }}}" />
                        {{ $errors->first('image_delete', '<br><span class="alert-msg">:message</span>') }}
                    </div>
                </div>
            @endif

            <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                <label class="col-md-2 control-label" for="image">@lang('general.image_upload')</label>
                <div class="col-md-5">
                    {{ Form::file('image') }}
                    {{ $errors->first('image', '<br><span class="alert-msg">:message</span>') }}
                </div>
            </div>



            <!-- Form actions -->
            <div class="form-group">
            <label class="col-md-2 control-label"></label>
                <div class="col-md-7">
                    @if(isset($clone_vehicle))
                        <a class="btn btn-link" href="{{ URL::route('view/vehicle', $clone_vehicle->id) }}">@lang('button.cancel')</a>
                    @else
                        <a class="btn btn-link" href="{{ URL::route('view/vehicle', $vehicle->id) }}">@lang('button.cancel')</a>
                    @endif
                    <button type="submit" class="btn btn-success"><i class="fa fa-check icon-white"></i> @lang('general.save')</button>
                </div>
            </div>
</form>
</div>
@stop
