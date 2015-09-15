@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
Locations ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('create/location') }}" class="btn btn-success pull-right"><i class="fa fa-plus icon-white"></i>  @lang('general.create')</a>
        <h3>@lang('admin/locations/table.locations')</h3>
    </div>
</div>

<div class="row form-wrapper">

<table id="example">
    <thead>
        <tr role="row">
            <th class="col-md-2">@lang('admin/locations/table.name')</th>
            <th class="col-md-2">@lang('admin/locations/table.parent')</th>
            <th class="col-md-1">@lang('general.assets')</th>
            <th class="col-md-3">@lang('admin/locations/table.address')</th>
            <th class="col-md-2">@lang('admin/locations/table.city'),
             @lang('admin/locations/table.state')
            @lang('admin/locations/table.country')</th>
            <th class="col-md-1 actions">@lang('table.actions')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($locations as $location)
        <tr>
            <td>{{{ $location->name }}}</td>
            <td>
              @if ($location->parent)
                {{{ $location->parent->name }}}
              @endif
            </td>
            <td>{{{ ($location->assets->count() + $location->assignedassets->count()) }}}</td>
            <td>{{{ $location->address }}}
            	@if($location->address2 != '')
            		, {{{ $location->address2 }}}
            	@endif
            </td>
            <td>{{{ $location->city }}}, {{{ strtoupper($location->state) }}}  {{{ strtoupper($location->country) }}}  </td>
            <td>
                <a href="{{ route('update/location', $location->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-pencil icon-white"></i></a>
                <a data-html="false" class="btn delete-asset btn-danger btn-sm" data-toggle="modal" href="{{ route('delete/location', $location->id) }}" data-content="@lang('admin/locations/message.delete.confirm')"
                data-title="@lang('general.delete')
                 {{ htmlspecialchars($location->name) }}?" onClick="return false;"><i class="fa fa-trash icon-white"></i></a>

            </td>
        </tr>
        @endforeach
    </tbody>
</table>
</div>


@stop
