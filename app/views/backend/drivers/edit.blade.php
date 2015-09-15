@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
    @if ($driver->id)
        @lang('admin/drivers/table.update') ::
    @else
        @lang('admin/drivers/table.create') ::
    @endif
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-9">
        <a href="{{ URL::previous() }}" class="btn-flat gray pull-right"><i class="fa fa-arrow-left icon-white"></i>  @lang('general.back')</a>
        <h3>
        @if ($driver->id)
            @lang('admin/drivers/table.update')
        @else
            @lang('admin/drivers/table.create')
        @endif
    </h3>
    </div>
</div>

<div class="user-profile">
    <div class="row profile">
        <div class="col-md-9">

           {{ Form::open(['method' => 'POST', 'files' => true, 'class' => 'form-horizontal', 'autocomplete' => 'off' ]) }}
                <!-- CSRF Token -->
                {{ Form::token() }}

                        <!-- Name -->
                        <div class="form-group {{ $errors->has('name') ? ' has-error' : '' }}">
                            {{ HTML::decode(Form::label('name', Lang::get('admin/drivers/table.name').' <i class="fa fa-asterisk"></i>', array('class' => 'col-md-3 control-label'))) }}
                                <div class="col-md-6">
                                    {{Form::text('name', Input::old('name', $driver->name), array('class' => 'form-control')) }}
                                    {{ $errors->first('name', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('address') ? ' has-error' : '' }}">
                           {{ Form::label('address', Lang::get('admin/drivers/table.address'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('address', Input::old('address', $driver->address), array('class' => 'form-control')) }}
                                    {{ $errors->first('address', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('address2') ? ' has-error' : '' }}">
                            {{ Form::label('address2', ' ', array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('address2', Input::old('address2', $driver->address2), array('class' => 'form-control')) }}
                                    {{ $errors->first('address2', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('city') ? ' has-error' : '' }}">
                            {{ Form::label('city', Lang::get('admin/drivers/table.city'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('city', Input::old('city', $driver->city), array('class' => 'form-control')) }}
                                    {{ $errors->first('city', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('state') ? ' has-error' : '' }}">
                             {{ Form::label('state', Lang::get('admin/drivers/table.state'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('state', Input::old('state', $driver->state), array('class' => 'form-control')) }}
                                    {{ $errors->first('state', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('country') ? ' has-error' : '' }}">
                            {{ Form::label('country', Lang::get('admin/drivers/table.country'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-5">
                                    {{ Form::countries('country', Input::old('country', $driver->country), 'select2') }}
                                    {{ $errors->first('country', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('zip') ? ' has-error' : '' }}">
                            {{ Form::label('zip', Lang::get('admin/drivers/table.zip'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('zip', Input::old('zip', $driver->zip), array('class' => 'form-control')) }}
                                    {{ $errors->first('zip', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>


                        <div class="form-group {{ $errors->has('contact') ? ' has-error' : '' }}">
                            {{ Form::label('contact', Lang::get('admin/drivers/table.contact'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('contact', Input::old('contact', $driver->contact), array('class' => 'form-control')) }}
                                    {{ $errors->first('contact', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>


                        <div class="form-group {{ $errors->has('phone') ? ' has-error' : '' }}">
                            {{ Form::label('phone', Lang::get('admin/drivers/table.phone'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('phone', Input::old('phone', $driver->phone), array('class' => 'form-control')) }}
                                    {{ $errors->first('phone', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('fax') ? ' has-error' : '' }}">
                            {{ Form::label('fax', Lang::get('admin/drivers/table.fax'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('fax', Input::old('fax', $driver->fax), array('class' => 'form-control')) }}
                                    {{ $errors->first('fax', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('email') ? ' has-error' : '' }}">
                            {{ Form::label('email', Lang::get('admin/drivers/table.email'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('email', Input::old('email', $driver->email), array('class' => 'form-control')) }}
                                    {{ $errors->first('email', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <div class="form-group {{ $errors->has('url') ? ' has-error' : '' }}">
                            {{ Form::label('url', Lang::get('admin/drivers/table.url'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('url', Input::old('url', $driver->url), array('class' => 'form-control')) }}
                                    {{ $errors->first('url', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>


                        <div class="form-group {{ $errors->has('notes') ? ' has-error' : '' }}">
                            {{ Form::label('notes', Lang::get('admin/drivers/table.notes'), array('class' => 'col-md-3 control-label')) }}
                                <div class="col-md-6">
                                    {{Form::text('notes', Input::old('notes', $driver->notes), array('class' => 'form-control')) }}
                                    {{ $errors->first('notes', '<br><span class="alert-msg"><i class="fa fa-times"></i> :message</span>') }}
                                </div>
                        </div>

                        <!-- Image -->
                        @if ($driver->image)
                            <div class="form-group {{ $errors->has('image_delete') ? 'has-error' : '' }}">
                                <label class="col-md-3 control-label" for="image_delete">@lang('general.image_delete')</label>
                                <div class="col-md-5">
                                    {{ Form::checkbox('image_delete') }}
                                    <img src="/uploads/drivers/{{{ $driver->image }}}" />
                                {{ $errors->first('image_delete', '<br><span class="alert-msg">:message</span>') }}
                                </div>
                            </div>
                        @endif
            
                        <div class="form-group {{ $errors->has('image') ? 'has-error' : '' }}">
                            <label class="col-md-3 control-label" for="image">@lang('general.image_upload')</label>
                            <div class="col-md-5">
                                {{ Form::file('image') }}
                                {{ $errors->first('image', '<br><span class="alert-msg">:message</span>') }}
                            </div>
                        </div>
                        
                    <!-- Form actions -->
                    <div class="form-group">
                    {{ Form::label('', ' ', array('class' => 'col-md-3 control-label')) }}
                        <div class="col-md-7">
                            @if ($driver->id)
                            <a class="btn btn-link" href="{{ URL::previous() }}">@lang('button.cancel')</a>
                            @else
                            <a class="btn btn-link" href="{{ route('drivers') }}">@lang('button.cancel')</a>
                            @endif
                            <button type="submit" class="btn btn-success"><i class="fa fa-check icon-white"></i> @lang('general.save')</button>
                        </div>
                    </div>

            </form>

        </div>
    </div>
</div>

@stop
