@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
@lang('admin/drivers/table.view') -
{{{ $driver->name }}} ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('update/driver', $driver->id) }}" class="btn-flat white pull-right">
        @lang('admin/drivers/table.update')</a>
        <h3 class="name">
        @lang('admin/drivers/table.view_assets_for')
        {{{ $driver->name }}} </h3>
    </div>
</div>

<div class="user-profile">
<div class="row profile">
<div class="col-md-9 bio">
    <div class="profile-box">

            <!-- checked out drivers table -->
            <h6>Assets</h6>
            <br>
            @if (count($driver->assets) > 0)
           <table id="example">
            <thead>
                <tr role="row">
                        <th class="col-md-3">Name</th>
                        <th class="col-md-3">Asset Tag</th>
                        <th class="col-md-3">User</th>
                        <th class="col-md-2">Actions</th>
                    </tr>
                </thead>
                <tbody>

                    @foreach ($driver->assets as $driverassets)
                    <tr>
                        <td><a href="{{ route('view/hardware', $driverassets->id) }}">{{{ $driverassets->name }}}</a></td>
                        <td><a href="{{ route('view/hardware', $driverassets->id) }}">{{{ $driverassets->name }}}</a></td>
                        <td><a href="{{ route('view/hardware', $driverassets->id) }}">{{{ $driverassets->asset_tag }}}</a></td>
                        <td>
                        @if ($driverassets->assigneduser)
                        <a href="{{ route('view/user', $driverassets->assigned_to) }}">
                        {{{ $driverassets->assigneduser->fullName() }}}
                        </a>
                        @endif
                        </td>
                        <td>
                        @if ($driverassets->assigned_to != '')
                            <a href="{{ route('checkin/hardware', $driverassets->id) }}" class="btn-flat info">Checkin</a>
                        @else
                            <a href="{{ route('checkout/hardware', $driverassets->id) }}" class="btn-flat success">Checkout</a>
                        @endif
                        </td>

                    </tr>
                    @endforeach


                </tbody>
            </table>

            @else
            <div class="col-md-12">
                <div class="alert alert-info alert-block">
                    <i class="fa fa-info-circle"></i>
                    @lang('general.no_results')
                </div>
            </div>
            @endif
            <br>
            <br>
            <h6>Software</h6>
            <br>
            @if (count($driver->licenses) > 0)
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="col-md-4"><span class="line"></span>Name</th>
                        <th class="col-md-4"><span class="line"></span>Serial</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($driver->licenses as $license)
                    <tr>
                        <td><a href="{{ route('view/license', $license->id) }}">{{{ $license->name }}}</a></td>
                        <td><a href="{{ route('view/license', $license->id) }}">{{{ $license->serial }}}</a></td>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else

            <div class="col-md-12">
                <div class="alert alert-info alert-block">
                    <i class="fa fa-info-circle"></i>
                    @lang('general.no_results')
                </div>
            </div>
            @endif
            <!-- Improvements -->
            <br>
            <br>
            <h6>Improvements</h6>
            <br>
            <!-- Improvement table -->
            @if (count($driver->improvements) > 0)
                <table class="table table-hover">
                    <thead>
                    <tr>
                        <th class="col-md-2"><span class="line"></span>@lang('admin/improvements/table.asset_name')</th>
                        <th class="col-md-2"><span class="line"></span>@lang('admin/improvements/form.improvement_type')</th>
                        <th class="col-md-2"><span class="line"></span>@lang('admin/improvements/form.start_date')</th>
                        <th class="col-md-2"><span class="line"></span>@lang('admin/improvements/form.completion_date')</th>
                        <th class="col-md-2"><span class="line"></span>@lang('admin/improvements/table.is_warranty')</th>
                        <th class="col-md-2"><span class="line"></span>@lang('admin/improvements/form.cost')</th>
                        <th class="col-md-1"><span class="line"></span>@lang('table.actions')</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $totalCost = 0; ?>
                    @foreach ($driver->improvements as $improvement)
                        @if (is_null($improvement->deleted_at))
                            <tr>
                                <td><a href="{{ route('view/hardware', $improvement->asset_id) }}">{{{ $improvement->asset->name }}}</a></td>
                                <td>{{{ $improvement->improvement_type }}}</td>
                                <td>{{{ $improvement->start_date }}}</td>
                                <td>{{{ $improvement->completion_date }}}</td>
                                <td>{{{ $improvement->is_warranty ? Lang::get('admin/improvements/message.warranty') : Lang::get('admin/improvements/message.not_warranty') }}}</td>
                                <td>{{{ sprintf( Lang::get( 'general.currency' ) . '%01.2f', $improvement->cost) }}}</td>
                                <?php $totalCost += $improvement->cost; ?>
                                <td><a href="{{ route('update/improvement', $improvement->id) }}" class="btn btn-warning"><i class="fa fa-pencil icon-white"></i></a>
                                </td>
                            </tr>
                        @endif
                    @endforeach
                    </tbody>
                    <tfoot>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{{sprintf(Lang::get( 'general.currency' ) . '%01.2f', $totalCost)}}}</td>
                    </tr>
                    </tfoot>
                </table>
            @else
                <div class="col-md-12">
                    <div class="alert alert-info alert-block">
                        <i class="fa fa-info-circle"></i>
                        @lang('general.no_results')
                    </div>
                </div>
            @endif

    </div>
</div>


                    <!-- side address column -->
                    <div class="col-md-3 col-xs-12 address pull-right">
                    <h6>Contact:</h6>
                               <ul>

                                @if ($driver->contact)
                                    <li><i class="fa fa-user"></i>{{{ $driver->contact }}}</li>
                                @endif
                                @if ($driver->phone)
                                    <li><i class="fa fa-phone"></i>{{{ $driver->phone }}}</li>
                                @endif
                                @if ($driver->fax)
                                    <li><i class="fa fa-print"></i>{{{ $driver->fax }}}</li>
                                @endif


                                @if ($driver->email)
                                    <li><i class="fa fa-envelope-o"></i><a href="mailto:{{{ $driver->email }}}">
                                    {{{ $driver->email }}}
                                    </a></li>
                                @endif

                                @if ($driver->url)
                                    <li><i class="fa fa-globe"></i><a href="{{{ $driver->url }}}" target="_new">{{{ $driver->url }}}</a></li>
                                @endif

                                @if ($driver->address)
                                    <li><br>
                                    {{{ $driver->address }}}

                                    @if ($driver->address2)
                                        <br>
                                        {{{ $driver->address2 }}}
                                    @endif
                                    @if (($driver->city) || ($driver->state))
                                        <br>
                                        {{{ $driver->city }}} {{{ strtoupper($driver->state) }}} {{{ $driver->zip }}} {{{ strtoupper($driver->country) }}}
                                    @endif
                                    </li>
                                @endif

                                @if ($driver->notes)
                                    <li><i class="fa fa-comment"></i>{{{ $driver->notes }}}</li>
                                @endif

                                @if ($driver->image)
                                <li><br /><img src="/uploads/drivers/{{{ $driver->image }}}" /></li>
                                @endif

                                </ul>



                            </ul>



                            <ul>
                                <li><br><br /></li>
                            </ul>

                    </div>
@stop
