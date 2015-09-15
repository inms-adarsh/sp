@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
@lang('admin/drivers/table.drivers') ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('create/driver') }}" class="btn btn-success pull-right"><i class="fa fa-plus icon-white"></i>  @lang('general.create')</a>
        <h3>@lang('admin/drivers/table.drivers')</h3>
    </div>
</div>

<div class="user-profile">
<div class="row profile">
<div class="col-md-12">

@if ($drivers->count() >= 1)
<table id="example">
    <thead>
        <tr role="row">
            <th class="col-md-3">@lang('admin/drivers/table.name')</th>
            <th class="col-md-3">@lang('admin/drivers/table.address')</th>
            <th class="col-md-3">@lang('admin/drivers/table.contact')</th>
            <th class="col-md-3">@lang('admin/drivers/table.phone')</th>
           <!--  <th class="col-md-3">@lang('admin/drivers/table.assets')</th>
            <th class="col-md-3">@lang('admin/drivers/table.licenses')</th> -->
            <th class="col-md-2 actions">@lang('table.actions')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($drivers as $driver)
        <tr>
            <td><a href="{{ route('view/driver', $driver->id) }}">
            {{{ $driver->name }}}
            </a></td>
            <td>{{{ $driver->address }}}

            @if (($driver->address2) || ($driver->city) || ($driver->state))

                 {{{ $driver->city }}}
                 {{{ $driver->state }}}  {{{ $driver->zip }}}
            @endif
            </td>
            <td>
            @if ($driver->email)
                <a href="mailto:{{{ $driver->email }}}">
                {{{ $driver->contact }}}
                </a>
            @else {{{ $driver->contact }}}
            @endif
            </td>
            <td>{{{ $driver->phone }}}</td>
           
            <td>
                <a href="{{ route('update/driver', $driver->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-pencil icon-white"></i></a>
                <a data-html="false" class="btn delete-asset btn-danger btn-sm" data-toggle="modal" href="{{ route('delete/driver', $driver->id) }}" data-content="@lang('admin/drivers/message.delete.confirm')"
                data-title="@lang('general.delete')
                 {{ htmlspecialchars($driver->name) }}?" onClick="return false;"><i class="fa fa-trash icon-white"></i></a>


            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@else
        @lang('general.no_results')

        @endif
</div>



</div>


@stop
