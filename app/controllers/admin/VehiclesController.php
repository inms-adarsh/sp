<?php namespace Controllers\Admin;

use AdminController;
use Image;
use Input;
use Lang;
use Vehicle;
use Redirect;
use Setting;
use Sentry;
use DB;
use Depreciation;
use Manufacturer;
use Str;
use Validator;
use View;
use Datatable;

//use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class VehiclesController extends AdminController
{
    /**
     * Show a list of all the vehicles.
     *
     * @return View
     */
    public function getIndex()
    {
        // Show the page
        return View::make('backend/vehicles/index');
    }

/**
     * Vehicle create.
     *
     * @return View
     */
    public function getCreate()
    {
        // Show the page
       
        $category_list = categoryList();
        $view = View::make('backend/vehicles/edit');
        $view->with('category_list',$category_list);
      
        $view->with('vehicle',new Vehicle);
        return $view;
    }


    /**
     * Vehicle create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {

        // Create a new manufacturer
        $vehicle = new Vehicle;


        $validator = Validator::make(
            // Validator data goes here
            array(
                'unique_fields' => array(Input::get('vehicleno'))
            ),
            // Validator rules go here
            array(
                'unique_fields' => 'unique_multiple:vehicles,vehicleno'
            )
        );

        // attempt validation
        if ($validator->fails())
        {
            // The given data did not pass validation
            return Redirect::back()->withInput()->with('error', Lang::get('admin/vehicles/message.create.duplicate_set'));;
        }



        $validator = Validator::make(Input::all(), $vehicle->validationRules());

        // attempt validation
        if ($validator->fails())
        {
            // The given data did not pass validation
            return Redirect::back()->withInput()->withErrors($validator->messages());
        }
        // attempt validation
        else {

           

            // Save the vehicle data
            $vehicle->vehicleno            	= e(Input::get('vehicleno'));
            $vehicle->category_id    		= e(Input::get('category_id'));
            $vehicle->user_id          	= Sentry::getId();
            


            if (Input::file('image')) {
                $image = Input::file('image');
                $file_name = str_random(25).".".$image->getClientOriginalExtension();
                $path = public_path('uploads/vehicles/'.$file_name);
                Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $vehicle->image = $file_name;
            }

            // Was it created?
            if($vehicle->save()) {
                // Redirect to the new vehicle  page
                return Redirect::to("hardware/vehicles")->with('success', Lang::get('admin/vehicles/message.create.success'));
            }
        }

        // Redirect to the vehicle create page
        return Redirect::to('hardware/vehicles/create')->with('error', Lang::get('admin/vehicles/message.create.error'));

    }

    public function store()
    {
      //COPYPASTA!!!! FIXME
      $vehicle = new Vehicle;

      $settings=Input::all();
      $settings['eol']=0;
      //

      $validator = Validator::make($settings, $vehicle->validationRules());
      if ($validator->fails())
      {
          // The given data did not pass validation
          return JsonResponse::create(["error" => "Failed validation: ".print_r($validator->messages()->all('<li>:message</li>'),true)],500);
      } else {
       $vehicle->category_id = e(Input::get('category_id'));
        $vehicle->vehicleno = e(Input::get('vehicleno'));
        $vehicle->user_id = Sentry::getUser()->id;
      
        if($vehicle->save()) {
          return JsonResponse::create($vehicle);
        } else {
          return JsonResponse::create(["error" => "Couldn't save Vehicle"],500);
        }
      }
    }

    /**
     * Vehicle update.
     *
     * @param  int  $vehicleId
     * @return View
     */
    public function getEdit($vehicleId = null)
    {
        // Check if the vehicle exists
        if (is_null($vehicle = Vehicle::find($vehicleId))) {
            // Redirect to the vehicle management page
            return Redirect::to('assets/vehicles')->with('error', Lang::get('admin/vehicles/message.does_not_exist'));
        }

       $category_list = array('' => '') + DB::table('categories')->whereNull('deleted_at')->lists('name', 'id');
        $view = View::make('backend/vehicles/edit', compact('vehicle'));
        $view->with('category_list',$category_list);
        return $view;
    }


    /**
     * Vehicle update form processing page.
     *
     * @param  int  $vehicleId
     * @return Redirect
     */
    public function postEdit($vehicleId = null)
    {
        // Check if the vehicle exists
        if (is_null($vehicle = Vehicle::find($vehicleId))) {
            // Redirect to the vehicles management page
            return Redirect::to('admin/vehicles')->with('error', Lang::get('admin/vehicles/message.does_not_exist'));
        }

          //attempt to validate
        $validator = Validator::make(Input::all(), $vehicle->validationRules($vehicleId));

        if ($validator->fails())
        {
            // The given data did not pass validation
            return Redirect::back()->withInput()->withErrors($validator->messages());
        }
        // attempt validation
        else {

      
            // Update the vehicle data
         
            $vehicle->vehicleno            	= e(Input::get('vehicleno'));
            $vehicle->category_id    		= e(Input::get('category_id'));
      
            if (Input::file('image')) {
                $image = Input::file('image');
                $file_name = str_random(25).".".$image->getClientOriginalExtension();
                $path = public_path('uploads/vehicles/'.$file_name);
                Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $vehicle->image = $file_name;
            }

            if (Input::get('image_delete') == 1 && Input::file('image') == "") {
                $vehicle->image = NULL;
            }

            // Was it created?
            if($vehicle->save()) {
                // Redirect to the new vehicle  page
                return Redirect::to("hardware/vehicles")->with('success', Lang::get('admin/vehicles/message.update.success'));
            }
        }

        // Redirect to the vehicle create page
        return Redirect::to("hardware/vehicles/$vehicleId/edit")->with('error', Lang::get('admin/vehicles/message.update.error'));

    }

    /**
     * Delete the given vehicle.
     *
     * @param  int  $vehicleId
     * @return Redirect
     */
    public function getDelete($vehicleId)
    {
        // Check if the vehicle exists
        if (is_null($vehicle = Vehicle::find($vehicleId))) {
            // Redirect to the blogs management page
            return Redirect::to('hardware/vehicles')->with('error', Lang::get('admin/vehicles/message.not_found'));
        }

        if ($vehicle->assets->count() > 0) {
            // Throw an error that this vehicle is associated with assets
            return Redirect::to('hardware/vehicles')->with('error', Lang::get('admin/vehicles/message.assoc_users'));

        } else {
            // Delete the vehicle
            $vehicle->delete();

            // Redirect to the vehicles management page
            return Redirect::to('hardware/vehicles')->with('success', Lang::get('admin/vehicles/message.delete.success'));
        }
    }

    public function getRestore($vehicleId = null)
    {

		// Get user information
		$vehicle = Vehicle::withTrashed()->find($vehicleId);

		 if (isset($vehicle->id)) {

			// Restore the vehicle
			$vehicle->restore();

			// Prepare the success message
			$success = Lang::get('admin/vehicles/message.restore.success');

			// Redirect back
			return Redirect::back()->with('success', $success);

		 } else {
			 return Redirect::back()->with('error', Lang::get('admin/vehicles/message.not_found'));
		 }

    }


    /**
    *  Get the asset information to present to the vehicle view page
    *
    * @param  int  $assetId
    * @return View
    **/
    public function getView($vehicleId = null)
    {
        $vehicle = Vehicle::withTrashed()->find($vehicleId);

        if (isset($vehicle->id)) {
                return View::make('backend/vehicles/view', compact('vehicle'));
        } else {
            // Prepare the error message
            $error = Lang::get('admin/vehicles/message.does_not_exist', compact('id'));

            // Redirect to the user management page
            return Redirect::route('vehicles')->with('error', $error);
        }


    }

        public function getClone($vehicleId = null)
    {
        // Check if the vehicle exists
        if (is_null($vehicle_to_clone = Vehicle::find($vehicleId))) {
            // Redirect to the vehicle management page
            return Redirect::to('assets/vehicles')->with('error', Lang::get('admin/vehicles/message.does_not_exist'));
        }

        $vehicle = clone $vehicle_to_clone;
        $vehicle->id = null;

        // Show the page
         $category_list = array('' => '') + DB::table('categories')->whereNull('deleted_at')->lists('name', 'id');
        $view = View::make('backend/vehicles/edit');
        $view->with('category_list',$category_list);
        $view->with('vehicle',$vehicle);
        $view->with('clone_vehicle',$vehicle_to_clone);
        return $view;

    }

    public function getDatatable($status = null)
    {
        $vehicles = Vehicle::orderBy('created_at', 'DESC')->with('category');
        ($status != 'Deleted') ?: $vehicles->withTrashed()->Deleted();;
        $vehicles = $vehicles->get();

        $actions = new \Chumper\Datatable\Columns\FunctionColumn('actions', function($vehicles) {
            if($vehicles->deleted_at=='') {
                return '<a href="'.route('update/vehicle', $vehicles->id).'" class="btn btn-warning btn-sm" style="margin-right:5px;"><i class="fa fa-pencil icon-white"></i></a><a data-html="false" class="btn delete-asset btn-danger btn-sm" data-toggle="modal" href="'.route('delete/vehicle', $vehicles->id).'" data-content="'.Lang::get('admin/vehicles/message.delete.confirm').'" data-title="'.Lang::get('general.delete').' '.htmlspecialchars($vehicles->name).'?" onClick="return false;"><i class="fa fa-trash icon-white"></i></a>';
            } else {
                return '<a href="'.route('restore/vehicle', $vehicles->id).'" class="btn btn-warning btn-sm"><i class="fa fa-recycle icon-white"></i></a>';
            }
        });

        return Datatable::collection($vehicles)
        /*->addColumn('name', function ($vehicles) {
            return link_to('/hardware/vehicles/'.$vehicles->id.'/view', $vehicles->name);
        })*/
        ->showColumns('vehicleno')
        ->addColumn('category', function($vehicles) {
            return ($vehicles->category) ? $vehicles->category->name : '';
        })
        ->addColumn($actions)
        ->searchColumns('vehicleno','category','actions')
        ->orderColumns('vehicleno','category','actions')
        ->make();
    }


    public function getDataView($vehicleID)
    {
        $vehicle = Vehicle::withTrashed()->find($vehicleID);
        $vehicleassets = $vehicle->assets;

        $actions = new \Chumper\Datatable\Columns\FunctionColumn('actions', function ($vehicleassets)
            {
                if (($vehicleassets->assigned_to !='') && ($vehicleassets->assigned_to > 0)) {
                    return '<a href="'.route('checkin/hardware', $vehicleassets->id).'" class="btn btn-primary btn-sm">'.Lang::get('general.checkin').'</a>';
                } else {
                    return '<a href="'.route('checkout/hardware', $vehicleassets->id).'" class="btn btn-info btn-sm">'.Lang::get('general.checkout').'</a>';
                }
            });

        return Datatable::collection($vehicleassets)
        ->addColumn('name', function ($vehicleassets) {
           return link_to('/hardware/'.$vehicleassets->id.'/view', $vehicleassets->showAssetName());
          // return $vehicleassets->name;
        })
        ->addColumn('asset_tag', function ($vehicleassets) {
            return link_to('/hardware/'.$vehicleassets->id.'/view', $vehicleassets->asset_tag);
        })
        ->showColumns('serial')
        ->addColumn('assigned_to', function ($vehicleassets) {
            if ($vehicleassets->assigned_to) {
                return link_to('/admin/users/'.$vehicleassets->assigned_to.'/view', $vehicleassets->assigneduser->fullName());
            }
        })
        ->addColumn($actions)
        ->searchColumns('name','asset_tag','serial','assigned_to','actions')
        ->orderColumns('name','asset_tag','serial','assigned_to','actions')
        ->make();
    }

}
