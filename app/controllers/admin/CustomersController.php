<?php namespace Controllers\Admin;

use AdminController;
use Image;
use AssetMaintenance;
use Input;
use Lang;
use Customer;
use Redirect;
use Setting;
use Sentry;
use Str;
use Validator;
use View;

use Symfony\Component\HttpFoundation\JsonResponse;


class CustomersController extends AdminController
{
    /**
     * Show a list of all customers
     *
     * @return View
     */
    public function getIndex()
    {
        // Grab all the customers
        $customers = Customer::orderBy('created_at', 'DESC')->get();

        // Show the page
        return View::make('backend/customers/index', compact('customers'));
    }


    /**
     * Customer create.
     *
     * @return View
     */
    public function getCreate()
    {
        return View::make('backend/customers/edit')->with('customer', new Customer);
    }


    /**
     * Customer create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {

        // get the POST data
        $new = Input::all();

        // Create a new customer
        $customer = new Customer;

        // attempt validation
        if ($customer->validate($new)) {

            // Save the location data
            $customer->name                 = e(Input::get('name'));
            $customer->address              = e(Input::get('address'));
            $customer->address2             = e(Input::get('address2'));
            $customer->city                 = e(Input::get('city'));
            $customer->state                = e(Input::get('state'));
            $customer->country              = e(Input::get('country'));
            $customer->zip                  = e(Input::get('zip'));
            $customer->contact              = e(Input::get('contact'));
            $customer->phone                = e(Input::get('phone'));
            $customer->fax                  = e(Input::get('fax'));
            $customer->email                = e(Input::get('email'));
            $customer->notes                = e(Input::get('notes'));
            $customer->url                  = $customer->addhttp(e(Input::get('url')));
            $customer->user_id              = Sentry::getId();

            if (Input::file('image')) {
                $image = Input::file('image');
                $file_name = str_random(25).".".$image->getClientOriginalExtension();
                $path = public_path('uploads/customers/'.$file_name);
                Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $customer->image = $file_name;
            }

            // Was it created?
            if($customer->save()) {
                // Redirect to the new customer  page
                return Redirect::to("admin/settings/customers")->with('success', Lang::get('admin/customers/message.create.success'));
            }
        } else {
            // failure
            $errors = $customer->errors();
            return Redirect::back()->withInput()->withErrors($errors);
        }

        // Redirect to the customer create page
        return Redirect::to('admin/settings/customers/create')->with('error', Lang::get('admin/customers/message.create.error'));

    }
    
    public function store()
    {
      $customer=new Customer;
      $new=Input::all();
      $validator = Validator::make($new, $customer->validationRules());
      if($validator->fails()) {
        return JsonResponse::create(["error" => "Failed validation: ".print_r($validator->messages()->all('<li>:message</li>'),true)],500);
      } else {
        //$customer->fill($new);
        $customer->name=$new['name'];
        $customer->user_id              = Sentry::getId();
      
        if($customer->save()) {
          return JsonResponse::create($customer);
        } else {
          return JsonResponse::create(["error" => "Couldn't save Customer"]);
        }
      }
    }

    /**
     * Customer update.
     *
     * @param  int  $customerId
     * @return View
     */
    public function getEdit($customerId = null)
    {
        // Check if the customer exists
        if (is_null($customer = Customer::find($customerId))) {
            // Redirect to the customer  page
            return Redirect::to('admin/settings/customers')->with('error', Lang::get('admin/customers/message.does_not_exist'));
        }

        // Show the page
        return View::make('backend/customers/edit', compact('customer'));
    }


    /**
     * Customer update form processing page.
     *
     * @param  int  $customerId
     * @return Redirect
     */
    public function postEdit($customerId = null)
    {
        // Check if the customer exists
        if (is_null($customer = Customer::find($customerId))) {
            // Redirect to the customer  page
            return Redirect::to('admin/settings/customers')->with('error', Lang::get('admin/customers/message.does_not_exist'));
        }


          //attempt to validate
        $validator = Validator::make(Input::all(), $customer->validationRules($customerId));

        if ($validator->fails())
        {
            // The given data did not pass validation           
            return Redirect::back()->withInput()->withErrors($validator->messages());
        }
        // attempt validation
        else {

            // Save the  data
            $customer->name                 = e(Input::get('name'));
            $customer->address              = e(Input::get('address'));
            $customer->address2             = e(Input::get('address2'));
            $customer->city                 = e(Input::get('city'));
            $customer->state                = e(Input::get('state'));
            $customer->country              = e(Input::get('country'));
            $customer->zip                  = e(Input::get('zip'));
            $customer->contact              = e(Input::get('contact'));
            $customer->phone                = e(Input::get('phone'));
            $customer->fax                  = e(Input::get('fax'));
            $customer->email                = e(Input::get('email'));
            $customer->url                  = $customer->addhttp(e(Input::get('url')));
            $customer->notes                = e(Input::get('notes'));

            if (Input::file('image')) {
                $image = Input::file('image');
                $file_name = str_random(25).".".$image->getClientOriginalExtension();
                $path = public_path('uploads/customers/'.$file_name);
                Image::make($image->getRealPath())->resize(300, null, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->save($path);
                $customer->image = $file_name;
            }

            if (Input::get('image_delete') == 1 && Input::file('image') == "") {
                $customer->image = NULL;
            }

            // Was it created?
            if($customer->save()) {
                // Redirect to the new customer page
                return Redirect::to("admin/settings/customers")->with('success', Lang::get('admin/customers/message.update.success'));
            }
        } 

        // Redirect to the customer management page
        return Redirect::to("admin/settings/customers/$customerId/edit")->with('error', Lang::get('admin/customers/message.update.error'));

    }

    /**
     * Delete the given customer.
     *
     * @param  int  $customerId
     * @return Redirect
     */
    public function getDelete($customerId)
    {
        // Check if the customer exists
        if (is_null($customer = Customer::find($customerId))) {
            // Redirect to the customers page
            return Redirect::to('admin/settings/customers')->with('error', Lang::get('admin/customers/message.not_found'));
        }

        if ($customer->num_assets() > 0) {

            // Redirect to the asset management page
            return Redirect::to('admin/settings/customers')->with('error', Lang::get('admin/customers/message.assoc_users'));
        } else {

            // Delete the customer
            $customer->delete();

            // Redirect to the customers management page
        return Redirect::to('admin/settings/customers')->with('success', Lang::get('admin/customers/message.delete.success'));
        }

    }


    /**
    *  Get the asset information to present to the customer view page
    *
    * @param  int  $assetId
    * @return View
    **/
    public function getView($customerId = null)
    {
        $customer = Customer::find($customerId);

        if (isset($customer->id)) {
                return View::make('backend/customers/view', compact('customer'));
        } else {
            // Prepare the error message
            $error = Lang::get('admin/customers/message.does_not_exist', compact('id'));

            // Redirect to the user management page
            return Redirect::route('customers')->with('error', $error);
        }


    }



}
