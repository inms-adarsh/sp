@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')
@lang('admin/items/general.items') ::
@parent
@stop

{{-- Page content --}}
@section('content')

<div class="row header">
    <div class="col-md-12">
        <a href="{{ route('create/item') }}" class="btn btn-success pull-right"><i class="fa fa-plus icon-white"></i> @lang('general.create')</a>
        <h3>@lang('admin/items/general.items')</h3>
    </div>
</div>

<div class="user-profile">
    <div class="row profile">
        <div class="col-md-9 bio">
            {{ Datatable::table()
                        ->addColumn(Lang::get('admin/items/table.title'),
                                Lang::get('general.type'),
                                Lang::get('table.actions'))
                    ->setOptions(
                                array(
                                    'language' => array(
                                    'search' => Lang::get('general.search'),
                                    'lengthMenu' => Lang::get('general.page_menu'),
                                    'loadingRecords' => Lang::get('general.loading'),
                                    'zeroRecords' => Lang::get('general.no_results'),
                                    'info' => Lang::get('general.pagination_info'), 
                                    'processing' => Lang::get('general.processing'),
                                    'paginate'=> array(
                                        'first'=>Lang::get('general.first'),
                                        'previous'=>Lang::get('general.previous'),
                                        'next'=>Lang::get('general.next'),
                                        'last'=>Lang::get('general.last'),
                                        ),
                                    ),
                                        'sAjaxSource'=> route('api.items.list'),
                                        'dom' =>'CT<"clear">lfrtip',
                                        'colVis'=> array('showAll'=>'Show All','restore'=>'Restore','exclude'=>array(2),'activate'=>'mouseover'),
                                        'columnDefs'=> array(array('bSortable'=>false,'targets'=>array(2))),
                                        'order'=>array(array(0,'asc')),
                                    )
                                )
                        ->render() }}
            </div>


        <!-- side address column -->
        <div class="col-md-3 col-xs-12 address pull-right">
                <br /><br />
                <h6>@lang('admin/items/general.about_items')</h6>
                <p>@lang('admin/items/general.about_items_details') </p>
        </div>
    </div>
</div>
@stop
