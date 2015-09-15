@extends('backend/layouts/default')

{{-- Page title --}}
@section('title')

 {{{ $item->name }}}
 @lang('general.item') ::
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
                    <li><a href="{{ route('update/model', $item->id) }}">@lang('admin/items/general.edit')</a></li>
                    <li><a href="{{ route('clone/model', $item->id) }}">@lang('admin/items/general.clone')</a></li>
                    <li><a href="{{ route('create/hardware', $item->id) }}">@lang('admin/hardware/form.create')</a></li>
            </ul>
        </div>
        <h3>
            {{{ $item->name }}}
 @lang('general.item')

        </h3>
    </div>
</div>

<div class="user-profile">
<div class="row profile">
<div class="col-md-12 bio">


                            <!-- checked out items table -->
                            @if (count($item->assets) > 0)
                              {{ Datatable::table()
                                ->addColumn(Lang::get('general.name'),
                                            Lang::get('general.asset_tag'),
                                            Lang::get('general.user'), 
                                            Lang::get('table.actions'))
                                ->setOptions(
                                        array(
                                            'sAjaxSource'=>route('api.items.view', $item->id),
                                            'dom' =>'T<"clear">lfrtip',
                                            'columnDefs'=> array(
                                                array('bSortable'=>false,'targets'=>array(3)),
                                                array('width'=>'auto','targets'=>array(3)),
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


@stop
