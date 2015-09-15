<?php namespace Controllers\Admin;

use AdminController;
use Input;
use Lang;
use Redirect;
use Setting;
use DB;
use Sentry;
use Load;
use Str;
use Validator;
use View;
use User;
use Actionlog;
use Mail;
use Datatable;
use Slack;
use Config;

class LoadsController extends AdminController
{
    /**
     * Show a list of all the loads.
     *
     * @return View
     */

    public function getIndex()
    {
        return View::make('backend/loads/index');
    }


    /**
     * Load create.
     *
     * @return View
     */
    public function getCreate()
    {
        // Show the page
        $category_list = array('' => '') + DB::table('categories')->where('category_type','=','load')->whereNull('deleted_at')->orderBy('name','ASC')->lists('name', 'id');
        return View::make('backend/loads/edit')->with('load',new Load)->with('category_list',$category_list);
    }


    /**
     * Load create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {

        // create a new model instance
        $load = new Load();

        $validator = Validator::make(Input::all(), $load->rules);

        if ($validator->fails())
        {
            // The given data did not pass validation
            return Redirect::back()->withInput()->withErrors($validator->messages());
        }
        else{

            // Update the load data
            $load->name            		= e(Input::get('name'));
           
            $load->qty            			= e(Input::get('qty'));
            $load->user_id          		= Sentry::getId();

            // Was the load created?
            if($load->save()) {
                // Redirect to the new load  page
                return Redirect::to("admin/loads")->with('success', Lang::get('admin/loads/message.create.success'));
            }
        }

        // Redirect to the load create page
        return Redirect::to('admin/loads/create')->with('error', Lang::get('admin/loads/message.create.error'));


    }

    /**
     * Load update.
     *
     * @param  int  $loadId
     * @return View
     */
    public function getEdit($loadId = null)
    {
        // Check if the load exists
        if (is_null($load = Load::find($loadId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/loads')->with('error', Lang::get('admin/loads/message.does_not_exist'));
        }

	
        return View::make('backend/loads/edit', compact('load'));
    }


    /**
     * Load update form processing page.
     *
     * @param  int  $loadId
     * @return Redirect
     */
    public function postEdit($loadId = null)
    {
        // Check if the blog post exists
        if (is_null($load = Load::find($loadId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/loads')->with('error', Lang::get('admin/loads/message.does_not_exist'));
        }


        // get the POST data
        $new = Input::all();

        // attempt validation
        $validator = Validator::make(Input::all(), $load->validationRules($loadId));


        if ($validator->fails())
        {
            // The given data did not pass validation
            return Redirect::back()->withInput()->withErrors($validator->messages());
        }
        // attempt validation
        else {

            // Update the load data
            $load->name            		= e(Input::get('name'));
            $load->qty            			= e(Input::get('qty'));

            // Was the load created?
            if($load->save()) {
                // Redirect to the new load page
                return Redirect::to("admin/loads")->with('success', Lang::get('admin/loads/message.update.success'));
            }
        }

        // Redirect to the load management page
        return Redirect::to("admin/loads/$loadID/edit")->with('error', Lang::get('admin/loads/message.update.error'));

    }

    /**
     * Delete the given load.
     *
     * @param  int  $loadId
     * @return Redirect
     */
    public function getDelete($loadId)
    {
        // Check if the blog post exists
        if (is_null($load = Load::find($loadId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/loads')->with('error', Lang::get('admin/loads/message.not_found'));
        }


		if ($load->hasUsers() > 0) {
			 return Redirect::to('admin/loads')->with('error', Lang::get('admin/loads/message.assoc_users', array('count'=> $load->hasUsers())));
		} else {
			$load->delete();

            // Redirect to the locations management page
            return Redirect::to('admin/loads')->with('success', Lang::get('admin/loads/message.delete.success'));

		}





    }



    /**
    *  Get the load information to present to the load view page
    *
    * @param  int  $loadId
    * @return View
    **/
    public function getView($loadID = null)
    {
        $load = Load::find($loadID);

        if (isset($load->id)) {
                return View::make('backend/loads/view', compact('load'));
        } else {
            // Prepare the error message
            $error = Lang::get('admin/loads/message.does_not_exist', compact('id'));

            // Redirect to the user management page
            return Redirect::route('loads')->with('error', $error);
        }


    }

    /**
    * Check out the load to a person
    **/
    public function getCheckout($loadId)
    {
        // Check if the load exists
        if (is_null($load = Load::find($loadId))) {
            // Redirect to the load management page with error
            return Redirect::to('loads')->with('error', Lang::get('admin/loads/message.not_found'));
        }

        // Get the dropdown of users and then pass it to the checkout view
        $users_list = array('' => 'Select a User') + DB::table('users')->select(DB::raw('concat(last_name,", ",first_name," (",username,")") as full_name, id'))->whereNull('deleted_at')->orderBy('last_name', 'asc')->orderBy('first_name', 'asc')->lists('full_name', 'id');

        return View::make('backend/loads/checkout', compact('load'))->with('users_list',$users_list);

    }

    /**
    * Check out the load to a person
    **/
    public function postCheckout($loadId)
    {
        // Check if the load exists
        if (is_null($load = Load::find($loadId))) {
            // Redirect to the load management page with error
            return Redirect::to('loads')->with('error', Lang::get('admin/loads/message.not_found'));
        }

		$admin_user = Sentry::getUser();
        $assigned_to = e(Input::get('assigned_to'));


        // Declare the rules for the form validation
        $rules = array(
            'assigned_to'   => 'required|min:1'
        );

        // Create a new validator instance from our validation rules
        $validator = Validator::make(Input::all(), $rules);

        // If validation fails, we'll exit the operation now.
        if ($validator->fails()) {
            // Ooops.. something went wrong
            return Redirect::back()->withInput()->withErrors($validator);
        }


        // Check if the user exists
        if (is_null($user = User::find($assigned_to))) {
            // Redirect to the load management page with error
            return Redirect::to('admin/loads')->with('error', Lang::get('admin/loads/message.user_does_not_exist'));
        }

        // Update the load data
        $load->assigned_to            		= e(Input::get('assigned_to'));

        $load->users()->attach($load->id, array(
        'load_id' => $load->id,
        'assigned_to' => e(Input::get('assigned_to'))));

            $logaction = new Actionlog();
            $logaction->load_id = $load->id;
            $logaction->checkedout_to = $load->assigned_to;
            $logaction->asset_type = 'load';
            $logaction->location_id = $user->location_id;
            $logaction->user_id = Sentry::getUser()->id;
            $logaction->note = e(Input::get('note'));

            $settings = Setting::getSettings();

			if ($settings->slack_endpoint) {


				$slack_settings = [
				    'username' => $settings->botname,
				    'channel' => $settings->slack_channel,
				    'link_names' => true
				];

				$client = new \Maknz\Slack\Client($settings->slack_endpoint,$slack_settings);

				try {
						$client->attach([
						    'color' => 'good',
						    'fields' => [
						        [
						            'title' => 'Checked Out:',
						            'value' => strtoupper($logaction->asset_type).' <'.Config::get('app.url').'/admin/loads/'.$load->id.'/view'.'|'.$load->name.'> checked out to <'.Config::get('app.url').'/admin/users/'.$user->id.'/view|'.$user->fullName().'> by <'.Config::get('app.url').'/admin/users/'.$admin_user->id.'/view'.'|'.$admin_user->fullName().'>.'
						        ],
						        [
						            'title' => 'Note:',
						            'value' => e($logaction->note)
						        ],



						    ]
						])->send('Load Checked Out');

					} catch (Exception $e) {

					}

			}



            $log = $logaction->logaction('checkout');

            $load_user = DB::table('loads_users')->where('assigned_to','=',$load->assigned_to)->where('load_id','=',$load->id)->first();

            $data['log_id'] = $logaction->id;
            $data['eula'] = $load->getEula();
            $data['first_name'] = $user->first_name;
            $data['item_name'] = $load->name;
            $data['checkout_date'] = $logaction->created_at;
            $data['item_tag'] = '';
            $data['expected_checkin'] = '';
            $data['note'] = $logaction->note;
            $data['require_acceptance'] = $load->requireAcceptance();


            if (($load->requireAcceptance()=='1')  || ($load->getEula())) {

	            Mail::send('emails.accept-asset', $data, function ($m) use ($user) {
	                $m->to($user->email, $user->first_name . ' ' . $user->last_name);
	                $m->subject('Confirm load delivery');
	            });
            }

            // Redirect to the new load page
            return Redirect::to("admin/loads")->with('success', Lang::get('admin/loads/message.checkout.success'));



    }


    public function getDatatable()
    {
        $loads = Load::select(array('id','name','qty'))
        ->whereNull('deleted_at')
        ->orderBy('created_at', 'DESC');

        $loads = $loads->get();

        $actions = new \Chumper\Datatable\Columns\FunctionColumn('actions',function($loads)
            {
                return '<a href="'.route('checkout/load', $loads->id).'" style="margin-right:5px;" class="btn btn-info btn-sm" '.(($loads->numRemaining() > 0 ) ? '' : ' disabled').'>'.Lang::get('general.checkout').'</a><a href="'.route('update/load', $loads->id).'" class="btn btn-warning btn-sm" style="margin-right:5px;"><i class="fa fa-pencil icon-white"></i></a><a data-html="false" class="btn delete-asset btn-danger btn-sm" data-toggle="modal" href="'.route('delete/load', $loads->id).'" data-content="'.Lang::get('admin/loads/message.delete.confirm').'" data-title="'.Lang::get('general.delete').' '.htmlspecialchars($loads->name).'?" onClick="return false;"><i class="fa fa-trash icon-white"></i></a>';
            });

        return Datatable::collection($loads)
        ->addColumn('name',function($loads)
            {
                return link_to('admin/loads/'.$loads->id.'/view', $loads->name);
            })
        ->addColumn('qty',function($loads)
            {
                return $loads->qty;
            })
        
        ->addColumn($actions)
        ->searchColumns('name','qty','actions')
        ->orderColumns('name','qty','actions')
        ->make();
    }

	public function getDataView($loadID)
	{
		$load = Load::find($loadID);
        $load_users = $load->users;


		return Datatable::collection($load_users)
		->addColumn('name',function($load_users)
			{
				return link_to('/admin/users/'.$load_users->id.'/view', $load_users->fullName());
			})
		->make();
    }

}
