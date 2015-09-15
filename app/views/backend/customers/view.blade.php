@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
@lang('admin/customers/table.view') -
{{{ $customer->name }}} ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('update/customer', $customer->id) }}" class="btn-flat white pull-right">
        @lang('admin/customers/table.update')</a>
        <h3 class="name">
        @lang('admin/customers/table.view_assets_for')
        {{{ $customer->name }}} </h3>
    </div>
</div>

<div class="user-profile">
<div class="row profile">
<div class="col-md-9 bio">
    <div class="profile-box">

            <!-- checked out customers table -->
            <h6>Assets</h6>
            <br>
            @if (count($customer->assets) > 0)
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

                    @foreach ($customer->assets as $customerassets)
                    <tr>
                        <td><a href="{{ route('view/hardware', $customerassets->id) }}">{{{ $customerassets->name }}}</a></td>
                        <td><a href="{{ route('view/hardware', $customerassets->id) }}">{{{ $customerassets->name }}}</a></td>
                        <td><a href="{{ route('view/hardware', $customerassets->id) }}">{{{ $customerassets->asset_tag }}}</a></td>
                        <td>
                        @if ($customerassets->assigneduser)
                        <a href="{{ route('view/user', $customerassets->assigned_to) }}">
                        {{{ $customerassets->assigneduser->fullName() }}}
                        </a>
                        @endif
                        </td>
                        <td>
                        @if ($customerassets->assigned_to != '')
                            <a href="{{ route('checkin/hardware', $customerassets->id) }}" class="btn-flat info">Checkin</a>
                        @else
                            <a href="{{ route('checkout/hardware', $customerassets->id) }}" class="btn-flat success">Checkout</a>
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
            @if (count($customer->licenses) > 0)
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="col-md-4"><span class="line"></span>Name</th>
                        <th class="col-md-4"><span class="line"></span>Serial</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer->licenses as $license)
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
            @if (count($customer->improvements) > 0)
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
                    @foreach ($customer->improvements as $improvement)
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

                                @if ($customer->contact)
                                    <li><i class="fa fa-user"></i>{{{ $customer->contact }}}</li>
                                @endif
                                @if ($customer->phone)
                                    <li><i class="fa fa-phone"></i>{{{ $customer->phone }}}</li>
                                @endif
                                @if ($customer->fax)
                                    <li><i class="fa fa-print"></i>{{{ $customer->fax }}}</li>
                                @endif


                                @if ($customer->email)
                                    <li><i class="fa fa-envelope-o"></i><a href="mailto:{{{ $customer->email }}}">
                                    {{{ $customer->email }}}
                                    </a></li>
                                @endif

                                @if ($customer->url)
                                    <li><i class="fa fa-globe"></i><a href="{{{ $customer->url }}}" target="_new">{{{ $customer->url }}}</a></li>
                                @endif

                                @if ($customer->address)
                                    <li><br>
                                    {{{ $customer->address }}}

                                    @if ($customer->address2)
                                        <br>
                                        {{{ $customer->address2 }}}
                                    @endif
                                    @if (($customer->city) || ($customer->state))
                                        <br>
                                        {{{ $customer->city }}} {{{ strtoupper($customer->state) }}} {{{ $customer->zip }}} {{{ strtoupper($customer->country) }}}
                                    @endif
                                    </li>
                                @endif

                                @if ($customer->notes)
                                    <li><i class="fa fa-comment"></i>{{{ $customer->notes }}}</li>
                                @endif

                                @if ($customer->image)
                                <li><br /><img src="/uploads/customers/{{{ $customer->image }}}" /></li>
                                @endif

                                </ul>



                            </ul>



                            <ul>
                                <li><br><br /></li>
                            </ul>

                    </div>
@stop
