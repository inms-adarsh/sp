@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
@lang('admin/customers/table.customers') ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('create/customer') }}" class="btn btn-success pull-right"><i class="fa fa-plus icon-white"></i>  @lang('general.create')</a>
        <h3>@lang('admin/customers/table.customers')</h3>
    </div>
</div>

<div class="user-profile">
<div class="row profile">
<div class="col-md-12">

@if ($customers->count() >= 1)
<table id="example">
    <thead>
        <tr role="row">
            <th class="col-md-3">@lang('admin/customers/table.name')</th>
            <th class="col-md-3">@lang('admin/customers/table.address')</th>
            <th class="col-md-3">@lang('admin/customers/table.contact')</th>
            <th class="col-md-3">@lang('admin/customers/table.phone')</th>
           <!--  <th class="col-md-3">@lang('admin/customers/table.assets')</th>
            <th class="col-md-3">@lang('admin/customers/table.licenses')</th> -->
            <th class="col-md-2 actions">@lang('table.actions')</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($customers as $customer)
        <tr>
            <td><a href="{{ route('view/customer', $customer->id) }}">
            {{{ $customer->name }}}
            </a></td>
            <td>{{{ $customer->address }}}

            @if (($customer->address2) || ($customer->city) || ($customer->state))

                 {{{ $customer->city }}}
                 {{{ $customer->state }}}  {{{ $customer->zip }}}
            @endif
            </td>
            <td>
            @if ($customer->email)
                <a href="mailto:{{{ $customer->email }}}">
                {{{ $customer->contact }}}
                </a>
            @else {{{ $customer->contact }}}
            @endif
            </td>
            <td>{{{ $customer->phone }}}</td>
           
            <td>
                <a href="{{ route('update/customer', $customer->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-pencil icon-white"></i></a>
                <a data-html="false" class="btn delete-asset btn-danger btn-sm" data-toggle="modal" href="{{ route('delete/customer', $customer->id) }}" data-content="@lang('admin/customers/message.delete.confirm')"
                data-title="@lang('general.delete')
                 {{ htmlspecialchars($customer->name) }}?" onClick="return false;"><i class="fa fa-trash icon-white"></i></a>


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
