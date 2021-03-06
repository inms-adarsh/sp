<?php

    namespace Controllers\Admin;

    use AdminController;
    use AssetMaintenance;
    use Carbon\Carbon;
    use Datatable;
    use DB;
    use Input;
    use Lang;
    use Log;
    use Mail;
    use Redirect;
    use Response;
    use Sentry;
    use Slack;
    use Str;
    use Supplier;
    use TCPDF;
    use Validator;
    use View;

    class AssetMaintenancesController extends AdminController
    {

        /**
         * getIndex
         *
         * @return mixed
         * @author  Vincent Sposato <vincent.sposato@gmail.com>
         * @version v1.0
         */
        public function getIndex()
        {

            return View::make( 'backend/asset_maintenances/index' );
        }

        /**
         * getDatatable
         * Gets the datatable for the index page
         *
         * @return mixed
         * @author  Vincent Sposato <vincent.sposato@gmail.com>
         * @version v1.0
         */
        public function getDatatable()
        {

            $assetMaintenances = AssetMaintenance::orderBy( 'created_at', 'DESC' )
                                            ->get();

            $actions = new \Chumper\Datatable\Columns\FunctionColumn( 'actions', function ( $assetMaintenances ) {

                return '<a href="' . route( 'update/asset_maintenance', $assetMaintenances->id )
                       . '" class="btn btn-warning btn-sm" style="margin-right:5px;"><i class="fa fa-pencil icon-white"></i></a><a data-html="false" class="btn delete-asset btn-danger btn-sm" data-toggle="modal" href="'
                       . route( 'delete/asset_maintenance', $assetMaintenances->id ) . '" data-content="'
                       . Lang::get( 'admin/asset_maintenances/message.delete.confirm' ) . '" data-title="'
                       . Lang::get( 'general.delete' ) . ' ' . htmlspecialchars( $assetMaintenances->title )
                       . '?" onClick="return false;"><i class="fa fa-trash icon-white"></i></a>';
            } );

            return Datatable::collection( $assetMaintenances )
                            ->addColumn( 'asset', function ( $assetMaintenances ) {

                                return link_to( '/hardware/' . $assetMaintenances->asset_id . '/view',
                                    mb_strimwidth( $assetMaintenances->asset->name, 0, 50, "..." ) );
                            } )
                            ->addColumn( 'supplier', function ( $assetMaintenances ) {

                                return link_to( '/admin/settings/suppliers/' . $assetMaintenances->supplier_id
                                                . '/view',
                                    mb_strimwidth( $assetMaintenances->supplier->name, 0, 50, "..." ) );
                            } )
                            ->addColumn( 'asset_maintenance_type', function ( $assetMaintenances ) {

                                return $assetMaintenances->asset_maintenance_type;
                            } )
                            ->addColumn( 'title', function ( $assetMaintenances ) {

                                return link_to( '/admin/asset_maintenances/' . $assetMaintenances->id . '/view',
                                    mb_strimwidth( $assetMaintenances->title, 0, 50, "..." ) );
                            } )
                            ->addColumn( 'start_date', function ( $assetMaintenances ) {

                                return $assetMaintenances->start_date;
                            } )
                            ->addColumn( 'completion_date', function ( $assetMaintenances ) {

                                return $assetMaintenances->completion_date;
                            } )
                            ->addColumn( 'asset_maintenance_time', function ( $assetMaintenances ) {

                                if (is_null( $assetMaintenances->asset_maintenance_time )) {
                                    $assetMaintenances->asset_maintenance_time = Carbon::now()
                                                                                       ->diffInDays( Carbon::parse( $assetMaintenances->start_date ) );
                                }

                                return intval( $assetMaintenances->asset_maintenance_time );
                            } )
                            ->addColumn( 'cost', function ( $assetMaintenances ) {

                                return sprintf( Lang::get( 'general.currency' ) . '%01.2f', $assetMaintenances->cost );
                            } )
                            ->addColumn( $actions )
                            ->searchColumns( 'asset', 'supplier', 'asset_maintenance_type', 'title', 'start_date',
                                'completion_date', 'asset_maintenance_time', 'cost', 'actions' )
                            ->orderColumns( 'asset', 'supplier', 'asset_maintenance_type', 'title', 'start_date',
                                'completion_date', 'asset_maintenance_time', 'cost', 'actions' )
                            ->make();
        }

        /**
         * getCreate
         *
         * @param null $assetId
         *
         * @return mixed
         * @author  Vincent Sposato <vincent.sposato@gmail.com>
         * @version v1.0
         */
        public function getCreate( $assetId = null )
        {

            // Prepare Asset Maintenance Type List
            $assetMaintenanceType = [
                                        '' => 'Select an asset maintenance type',
                                    ] + AssetMaintenance::getImprovementOptions();
            // Mark the selected asset, if it came in
            $selectedAsset = $assetId;
            // Get the possible assets using a left join to get a list of assets and some other helpful info
            $asset               = DB::table( 'assets' )
                                     ->leftJoin( 'users', 'users.id', '=', 'assets.assigned_to' )
                                     ->leftJoin( 'models', 'assets.model_id', '=', 'models.id' )
                                     ->select( 'assets.id', 'assets.name', 'first_name', 'last_name', 'asset_tag',
                                         DB::raw( 'concat(first_name," ",last_name) as full_name, assets.id as id, models.name as modelname' ) )
                                     ->whereNull( 'assets.deleted_at' )
                                     ->get();
            $asset_array         = json_decode( json_encode( $asset ), true );
            $asset_element[ '' ] = 'Please select an asset';
            // Build a list out of the data results
            for ($x = 0; $x < count( $asset_array ); $x++) {

                if ($asset_array[ $x ][ 'full_name' ] != '') {
                    $full_name = ' (' . $asset_array[ $x ][ 'full_name' ] . ') ' . $asset_array[ $x ][ 'modelname' ];
                } else {
                    $full_name = ' (Unassigned) ' . $asset_array[ $x ][ 'modelname' ];
                }
                $asset_element[ $asset_array[ $x ][ 'id' ] ] =
                    $asset_array[ $x ][ 'asset_tag' ] . ' - ' . $asset_array[ $x ][ 'name' ] . $full_name;
            }
            // Get Supplier List
            $supplier_list = [ '' => 'Select Supplier' ] + Supplier::orderBy( 'name', 'asc' )
                                                                   ->lists( 'name', 'id' );

            // Render the view
            return View::make( 'backend/asset_maintenances/edit' )
                       ->with( 'asset_list', $asset_element )
                       ->with( 'selectedAsset', $selectedAsset )
                       ->with( 'supplier_list', $supplier_list )
                       ->with( 'assetMaintenanceType', $assetMaintenanceType )
                       ->with( 'assetMaintenance', new AssetMaintenance );
        }

        /**
         * postCreate
         *
         * @return mixed
         * @author  Vincent Sposato <vincent.sposato@gmail.com>
         * @version v1.0
         */
        public function postCreate()
        {

            // get the POST data
            $new = Input::all();

            // create a new model instance
            $assetMaintenance = new AssetMaintenance();

            // attempt validation
            if ($assetMaintenance->validate( $new )) {

                if (e( Input::get( 'supplier_id' ) ) == '') {
                    $assetMaintenance->supplier_id = null;
                } else {
                    $assetMaintenance->supplier_id = e( Input::get( 'supplier_id' ) );
                }

                if (e( Input::get( 'is_warranty' ) ) == '') {
                    $assetMaintenance->is_warranty = 0;
                } else {
                    $assetMaintenance->is_warranty = e( Input::get( 'is_warranty' ) );
                }

                if (e( Input::get( 'cost' ) ) == '') {
                    $assetMaintenance->cost = '';
                } else {
                    $assetMaintenance->cost = ParseFloat( e( Input::get( 'cost' ) ) );
                }

                if (e( Input::get( 'notes' ) ) == '') {
                    $assetMaintenance->notes = null;
                } else {
                    $assetMaintenance->notes = e( Input::get( 'notes' ) );
                }

                // Save the asset maintenance data
                $assetMaintenance->asset_id               = e( Input::get( 'asset_id' ) );
                $assetMaintenance->asset_maintenance_type = e( Input::get( 'asset_maintenance_type' ) );
                $assetMaintenance->title                  = e( Input::get( 'title' ) );
                $assetMaintenance->start_date             = e( Input::get( 'start_date' ) );
                $assetMaintenance->completion_date        = e( Input::get( 'completion_date' ) );

                if (( $assetMaintenance->completion_date == "" )
                    || ( $assetMaintenance->completion_date == "0000-00-00" )
                ) {
                    $assetMaintenance->completion_date = null;
                }

                // Was the asset maintenance created?
                if ($assetMaintenance->save()) {

                    // Redirect to the new asset maintenance page
                    return Redirect::to( "admin/asset_maintenances" )
                                   ->with( 'success', Lang::get( 'admin/asset_maintenances/message.create.success' ) );
                }
            } else {
                // failure
                $errors = $assetMaintenance->errors();

                return Redirect::back()
                               ->withInput()
                               ->withErrors( $errors );
            }

            // Redirect to the asset maintenance create page
            return Redirect::to( 'admin/asset_maintenances/edit' )
                           ->with( 'error', Lang::get( 'admin/asset_maintenances/message.create.error' ) )
                           ->with( 'assetMaintenance', new AssetMaintenance );

        }

        /**
         * getEdit
         *
         * @param null $assetMaintenanceId
         *
         * @return mixed
         * @author  Vincent Sposato <vincent.sposato@gmail.com>
         * @version v1.0
         */
        public function getEdit( $assetMaintenanceId = null )
        {

            // Check if the asset maintenance exists
            if (is_null( $assetMaintenance = AssetMaintenance::find( $assetMaintenanceId ) )) {
                // Redirect to the improvement management page
                return Redirect::to( 'admin/asset_maintenances' )
                               ->with( 'error', Lang::get( 'admin/asset_maintenances/message.not_found' ) );
            }

            if ($assetMaintenance->completion_date == '0000-00-00') {
                $assetMaintenance->completion_date = null;
            }

            if ($assetMaintenance->start_date == '0000-00-00') {
                $assetMaintenance->start_date = null;
            }

            if ($assetMaintenance->cost == '0.00') {
                $assetMaintenance->cost = null;
            }

            // Prepare Improvement Type List
            $assetMaintenanceType = [
                                        '' => 'Select an improvement type',
                                    ] + AssetMaintenance::getImprovementOptions();

            // Get the possible assets using a left join to get a list of assets and some other helpful info
            $asset               = DB::table( 'assets' )
                                     ->leftJoin( 'users', 'users.id', '=', 'assets.assigned_to' )
                                     ->leftJoin( 'models', 'assets.model_id', '=', 'models.id' )
                                     ->select( 'assets.id', 'assets.name', 'first_name', 'last_name', 'asset_tag',
                                         DB::raw( 'concat(first_name," ",last_name) as full_name, assets.id as id, models.name as modelname' ) )
                                     ->whereNull( 'assets.deleted_at' )
                                     ->get();
            $asset_array         = json_decode( json_encode( $asset ), true );
            $asset_element[ '' ] = 'Please select an asset';
            // Build a list out of the data results
            for ($x = 0; $x < count( $asset_array ); $x++) {

                if ($asset_array[ $x ][ 'full_name' ] != '') {
                    $full_name = ' (' . $asset_array[ $x ][ 'full_name' ] . ') ' . $asset_array[ $x ][ 'modelname' ];
                } else {
                    $full_name = ' (Unassigned) ' . $asset_array[ $x ][ 'modelname' ];
                }
                $asset_element[ $asset_array[ $x ][ 'id' ] ] =
                    $asset_array[ $x ][ 'asset_tag' ] . ' - ' . $asset_array[ $x ][ 'name' ] . $full_name;
            }
            // Get Supplier List
            $supplier_list = [ '' => 'Select Supplier' ] + Supplier::orderBy( 'name', 'asc' )
                                                                   ->lists( 'name', 'id' );

            // Render the view
            return View::make( 'backend/asset_maintenances/edit' )
                       ->with( 'asset_list', $asset_element )
                       ->with( 'selectedAsset', null )
                       ->with( 'supplier_list', $supplier_list )
                       ->with( 'assetMaintenanceType', $assetMaintenanceType )
                       ->with( 'assetMaintenance', $assetMaintenance );

        }

        /**
         * postEdit
         *
         * @param null $assetMaintenanceId
         *
         * @return mixed
         * @author  Vincent Sposato <vincent.sposato@gmail.com>
         * @version v1.0
         */
        public function postEdit( $assetMaintenanceId = null )
        {

            // get the POST data
            $new = Input::all();

            // Check if the asset maintenance exists
            if (is_null( $assetMaintenance = AssetMaintenance::find( $assetMaintenanceId ) )) {
                // Redirect to the asset maintenance management page
                return Redirect::to( 'admin/asset_maintenances' )
                               ->with( 'error', Lang::get( 'admin/asset_maintenances/message.not_found' ) );
            }

            // attempt validation
            if ($assetMaintenance->validate( $new )) {

                if (e( Input::get( 'supplier_id' ) ) == '') {
                    $assetMaintenance->supplier_id = null;
                } else {
                    $assetMaintenance->supplier_id = e( Input::get( 'supplier_id' ) );
                }

                if (e( Input::get( 'is_warranty' ) ) == '') {
                    $assetMaintenance->is_warranty = 0;
                } else {
                    $assetMaintenance->is_warranty = e( Input::get( 'is_warranty' ) );
                }

                if (e( Input::get( 'cost' ) ) == '') {
                    $assetMaintenance->cost = '';
                } else {
                    $assetMaintenance->cost = ParseFloat( e( Input::get( 'cost' ) ) );
                }

                if (e( Input::get( 'notes' ) ) == '') {
                    $assetMaintenance->notes = null;
                } else {
                    $assetMaintenance->notes = e( Input::get( 'notes' ) );
                }

                // Save the asset maintenance data
                $assetMaintenance->asset_id               = e( Input::get( 'asset_id' ) );
                $assetMaintenance->asset_maintenance_type = e( Input::get( 'asset_maintenance_type' ) );
                $assetMaintenance->title                  = e( Input::get( 'title' ) );
                $assetMaintenance->start_date             = e( Input::get( 'start_date' ) );
                $assetMaintenance->completion_date        = e( Input::get( 'completion_date' ) );

                if (( $assetMaintenance->completion_date == "" )
                    || ( $assetMaintenance->completion_date == "0000-00-00" )
                ) {
                    $assetMaintenance->completion_date = null;
                    if (( $assetMaintenance->asset_maintenance_time !== 0 )
                        || ( !is_null( $assetMaintenance->asset_maintenance_time ) )
                    ) {
                        $assetMaintenance->asset_maintenance_time = null;
                    }
                }

                if (( $assetMaintenance->completion_date !== "" )
                    && ( $assetMaintenance->completion_date !== "0000-00-00" )
                    && ( $assetMaintenance->start_date !== "" )
                    && ( $assetMaintenance->start_date !== "0000-00-00" )
                ) {
                    $startDate                                = Carbon::parse( $assetMaintenance->start_date );
                    $completionDate                           = Carbon::parse( $assetMaintenance->completion_date );
                    $assetMaintenance->asset_maintenance_time = $completionDate->diffInDays( $startDate );
                }

                // Was the asset maintenance created?
                if ($assetMaintenance->save()) {

                    // Redirect to the new asset maintenance page
                    return Redirect::to( "admin/asset_maintenances" )
                                   ->with( 'success', Lang::get( 'admin/asset_maintenances/message.create.success' ) );
                }
            } else {
                // failure
                $errors = $assetMaintenance->errors();

                return Redirect::back()
                               ->withInput()
                               ->withErrors( $errors );
            }

            // Redirect to the improvement create page
            return Redirect::to( 'admin/asset_maintenances/edit' )
                           ->with( 'error', Lang::get( 'admin/asset_maintenances/message.create.error' ) )
                           ->with( 'assetMaintenance', $assetMaintenance );

        }

        /**
         * getDelete
         *
         * @param $assetMaintenanceId
         *
         * @return mixed
         * @author  Vincent Sposato <vincent.sposato@gmail.com>
         * @version v1.0
         */
        public function getDelete( $assetMaintenanceId )
        {

            // Check if the asset maintenance exists
            if (is_null( $assetMaintenance = AssetMaintenance::find( $assetMaintenanceId ) )) {
                // Redirect to the asset maintenance management page
                return Redirect::to( 'admin/asset_maintenances' )
                               ->with( 'error', Lang::get( 'admin/asset_maintenances/message.not_found' ) );
            }
            // Delete the asset maintenance
            $assetMaintenance->delete();

            // Redirect to the asset_maintenance management page
            return Redirect::to( 'admin/asset_maintenances' )
                           ->with( 'success', Lang::get( 'admin/asset_maintenances/message.delete.success' ) );
        }

        /**
         * getView
         *
         * @param $assetMaintenanceId
         *
         * @return mixed
         * @author  Vincent Sposato <vincent.sposato@gmail.com>
         * @version v1.0
         */
        public function getView( $assetMaintenanceId )
        {

            // Check if the asset maintenance exists
            if (is_null( $assetMaintenance = AssetMaintenance::find( $assetMaintenanceId ) )) {
                // Redirect to the asset maintenance management page
                return Redirect::to( 'admin/asset_maintenances' )
                               ->with( 'error', Lang::get( 'admin/asset_maintenances/message.not_found' ) );
            }

            return View::make( 'backend/asset_maintenances/view')->with('assetMaintenance', $assetMaintenance);
        }

    }