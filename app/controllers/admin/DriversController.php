<?php namespace Controllers\Admin;

use AdminController;
use Image;
use AssetMaintenance;
use Input;
use Lang;
use Driver;
use Redirect;
use Setting;
use Sentry;
use Str;
use Validator;
use View;

use Symfony\Component\HttpFoundation\JsonResponse;


class DriversController extends AdminController
{
    /**
     * Show a list of all drivers
     *
     * @return View
     */
    public function getIndex()
    {
        // Grab all the drivers
        $drivers = Driver::orderBy('created_at', 'DESC')->get();

        // Show the page
        return View::make('backend/drivers/index', compact('drivers'));
    }


    /**
     * Driver create.
     *
     * @return View
     */
    public function getCreate()
    {
        return View::make('backend/drivers/edit')->with('driver', new Driver);
    }


    /**
     * Driver create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {

        // get the POST data
        $new = Input::all();

        // Create a new driver
        $driver = new Driver;

        // attempt validation
        if ($driver->validate($new)) {

            // Save the location data
            $driver->name                 = e(Input::get('name'));
            $driver->address              = e(Input::get('address'));
            $driver->address2             = e(Input::get('address2'));
            $driver->city                 = e(Input::get('city'));
            $driver->state                = e(Input::get('state'));
            $driver->country              = e(Input::get('country'));
            $driver->zip                  = e(Input::get('zip'));
            $driver->contact              = e(Input::get('contact'));
            $driver->phone                = e(Input::get('phone'));
            $driver->fax                  = e(Input::get('fax'));
            $driver->email                = e(Input::get('email'));
            $driver->notes                = e(Input::get('notes'));
            $driver->url                  = $driver->addhttp(e(Input::get('url')));
            $driver->user_id              = Sentry::getId();

            if (Input::file('image')) {
                $image = Input::file('image');
                $file_name = str_random(25).".".$image->getClientOriginalExtension();
                $path = public_path('uploads/drivers/'.$file_name);
                Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $driver->image = $file_name;
            }

            // Was it created?
            if($driver->save()) {
                // Redirect to the new driver  page
                return Redirect::to("admin/settings/drivers")->with('success', Lang::get('admin/drivers/message.create.success'));
            }
        } else {
            // failure
            $errors = $driver->errors();
            return Redirect::back()->withInput()->withErrors($errors);
        }

        // Redirect to the driver create page
        return Redirect::to('admin/settings/drivers/create')->with('error', Lang::get('admin/drivers/message.create.error'));

    }
    
    public function store()
    {
      $driver=new Driver;
      $new=Input::all();
      $validator = Validator::make($new, $driver->validationRules());
      if($validator->fails()) {
        return JsonResponse::create(["error" => "Failed validation: ".print_r($validator->messages()->all('<li>:message</li>'),true)],500);
      } else {
        //$driver->fill($new);
        $driver->name=$new['name'];
        $driver->user_id              = Sentry::getId();
      
        if($driver->save()) {
          return JsonResponse::create($driver);
        } else {
          return JsonResponse::create(["error" => "Couldn't save Driver"]);
        }
      }
    }

    /**
     * Driver update.
     *
     * @param  int  $driverId
     * @return View
     */
    public function getEdit($driverId = null)
    {
        // Check if the driver exists
        if (is_null($driver = Driver::find($driverId))) {
            // Redirect to the driver  page
            return Redirect::to('admin/settings/drivers')->with('error', Lang::get('admin/drivers/message.does_not_exist'));
        }

        // Show the page
        return View::make('backend/drivers/edit', compact('driver'));
    }


    /**
     * Driver update form processing page.
     *
     * @param  int  $driverId
     * @return Redirect
     */
    public function postEdit($driverId = null)
    {
        // Check if the driver exists
        if (is_null($driver = Driver::find($driverId))) {
            // Redirect to the driver  page
            return Redirect::to('admin/settings/drivers')->with('error', Lang::get('admin/drivers/message.does_not_exist'));
        }


          //attempt to validate
        $validator = Validator::make(Input::all(), $driver->validationRules($driverId));

        if ($validator->fails())
        {
            // The given data did not pass validation           
            return Redirect::back()->withInput()->withErrors($validator->messages());
        }
        // attempt validation
        else {

            // Save the  data
            $driver->name                 = e(Input::get('name'));
            $driver->address              = e(Input::get('address'));
            $driver->address2             = e(Input::get('address2'));
            $driver->city                 = e(Input::get('city'));
            $driver->state                = e(Input::get('state'));
            $driver->country              = e(Input::get('country'));
            $driver->zip                  = e(Input::get('zip'));
            $driver->contact              = e(Input::get('contact'));
            $driver->phone                = e(Input::get('phone'));
            $driver->fax                  = e(Input::get('fax'));
            $driver->email                = e(Input::get('email'));
            $driver->url                  = $driver->addhttp(e(Input::get('url')));
            $driver->notes                = e(Input::get('notes'));

            if (Input::file('image')) {
                $image = Input::file('image');
                $file_name = str_random(25).".".$image->getClientOriginalExtension();
                $path = public_path('uploads/drivers/'.$file_name);
                Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $driver->image = $file_name;
            }

            if (Input::get('image_delete') == 1 && Input::file('image') == "") {
                $driver->image = NULL;
            }

            // Was it created?
            if($driver->save()) {
                // Redirect to the new driver page
                return Redirect::to("admin/settings/drivers")->with('success', Lang::get('admin/drivers/message.update.success'));
            }
        } 

        // Redirect to the driver management page
        return Redirect::to("admin/settings/drivers/$driverId/edit")->with('error', Lang::get('admin/drivers/message.update.error'));

    }

    /**
     * Delete the given driver.
     *
     * @param  int  $driverId
     * @return Redirect
     */
    public function getDelete($driverId)
    {
        // Check if the driver exists
        if (is_null($driver = Driver::find($driverId))) {
            // Redirect to the drivers page
            return Redirect::to('admin/settings/drivers')->with('error', Lang::get('admin/drivers/message.not_found'));
        }

        if ($driver->num_assets() > 0) {

            // Redirect to the asset management page
            return Redirect::to('admin/settings/drivers')->with('error', Lang::get('admin/drivers/message.assoc_users'));
        } else {

            // Delete the driver
            $driver->delete();

            // Redirect to the drivers management page
        return Redirect::to('admin/settings/drivers')->with('success', Lang::get('admin/drivers/message.delete.success'));
        }

    }


    /**
    *  Get the asset information to present to the driver view page
    *
    * @param  int  $assetId
    * @return View
    **/
    public function getView($driverId = null)
    {
        $driver = Driver::find($driverId);

        if (isset($driver->id)) {
                return View::make('backend/drivers/view', compact('driver'));
        } else {
            // Prepare the error message
            $error = Lang::get('admin/drivers/message.does_not_exist', compact('id'));

            // Redirect to the user management page
            return Redirect::route('drivers')->with('error', $error);
        }


    }



}
