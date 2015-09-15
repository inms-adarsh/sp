@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
@lang('admin/vehicles/table.view')
{{{ $vehicle->vehicle_tag }}} ::
@parent
@stop

{{-- Page content --}}
@section('content')


<div class="row header">
    <div class="col-md-12">
        <div class="btn-group pull-right">
           <button class="btn btn-default dropdown-toggle" data-toggle="dropdown">@lang('button.actions')
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu">
                @if ($vehicle->deleted_at=='')
                    <li><a href="{{ route('update/vehicle', $vehicle->id) }}">@lang('admin/vehicles/table.edit')</a></li>
                    <li><a href="{{ route('clone/vehicle', $vehicle->id) }}">@lang('admin/vehicles/table.clone')</a></li>
                    <li><a href="{{ route('create/hardware', $vehicle->id) }}">@lang('admin/hardware/form.create')</a></li>
                @else
                    <li><a href="{{ route('restore/vehicle', $vehicle->id) }}">@lang('admin/vehicles/general.restore')</a></li>
                @endif
            </ul>
        </div>
        <h3>

            @lang('admin/vehicles/table.view') -
            {{{ $vehicle->name }}}

        </h3>
    </div>
</div>

<div class="user-profile">
<div class="row profile">
<div class="col-md-9 bio">
    
    @if ($vehicle->deleted_at!='')
			<div class="alert alert-warning alert-block">
				<i class="fa fa-warning"></i>
				@lang('admin/vehicles/general.deleted', array('vehicle_id' => $vehicle->id))

			</div>

		@endif


                            <!-- checked out vehicles table -->
                            @if (count($vehicle->assets) > 0)
                            	{{ Datatable::table()
                                ->addColumn(Lang::get('general.name'),
                                            Lang::get('general.asset_tag'),
                                            Lang::get('admin/hardware/table.serial'),
                                            Lang::get('general.user'), 
                                            Lang::get('table.actions'))
                                ->setOptions(
                                        array(
                                            'sAjaxSource'=>route('api.vehicles.view', $vehicle->id),
                                            'dom' =>'CT<"clear">lfrtip',
                                            'colVis'=> array('showAll'=>'Show All','restore'=>'Restore','exclude'=>array(4),'activate'=>'mouseover'),
                                            'columnDefs'=> array(
                                                array('bSortable'=>false,'targets'=>array(4)),
                                                array('width'=>'auto','targets'=>array(4)),
                                                ),
                                            'order'=>array(array(0,'asc')),
                                        )
                                    )
                                ->render() }}
                            @else
                            <div class="col-md-9">
                                <div class="alert alert-info alert-block">
                                    <i class="fa fa-info-circle"></i>
                                    @lang('general.no_results')
                                </div>
                            </div>
                            @endif

                        </div>


                    <!-- side address column -->
                    <div class="col-md-3 col-xs-12 address pull-right">
                    <h6>More Info:</h6>
                               <ul>


                                @if ($vehicle->manufacturer)
                                <li>@lang('general.manufacturer'):
                                {{ $vehicle->manufacturer->name }}</li>
                                @endif

                                @if ($vehicle->vehicleno)
                                <li>@lang('general.vehicle_no'):
                                {{ $vehicle->vehicleno }}</li>
                                @endif

                                @if ($vehicle->depreciation)
                                <li>@lang('general.depreciation'):
                                {{ $vehicle->depreciation->name }} ({{ $vehicle->depreciation->months }}
                                @lang('general.months')
                                )</li>
                                @endif

                                @if ($vehicle->eol)
                                <li>@lang('general.eol'):
                                {{ $vehicle->eol }} 
                                @lang('general.months')</li>
                                @endif

                                @if ($vehicle->image)
                                <li><br /><img src="{{ Config::get('app.url') }}/uploads/vehicles/{{{ $vehicle->image }}}" /></li>
                                @endif
                                   
                                @if  ($vehicle->deleted_at!='')
                                   <li><br /><a href="{{ route('restore/vehicle', $vehicle->id) }}" class="btn-flat large info ">@lang('admin/vehicles/general.restore')</a></li>

                    	@endif

                            </ul>

                    </div>
@stop
