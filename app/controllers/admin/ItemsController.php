<?php namespace Controllers\Admin;

use AdminController;
use Input;
use Lang;
use Item;
use Redirect;
use Setting;
use DB;
use Sentry;
use Str;
use Validator;
use View;
use Datatable;

class ItemsController extends AdminController
{
    /**
     * Show a list of all the items.
     *
     * @return View
     */

    public function getIndex()
    {
        // Show the page
        return View::make('backend/items/index');
    }


    /**
     * Item create.
     *
     * @return View
     */
    public function getCreate()
    {
        // Show the page
         $item_types= itemTypeList();
        return View::make('backend/items/edit')->with('item',new Item)
        ->with('item_types',$item_types);
    }


    /**
     * Item create form processing.
     *
     * @return Redirect
     */
    public function postCreate()
    {

        // create a new model instance
        $item = new Item();

        $validator = Validator::make(Input::all(), $item->rules);

        if ($validator->fails())
        {
            // The given data did not pass validation
            return Redirect::back()->withInput()->withErrors($validator->messages());
        }
        else{

            // Update the item data
            $item->name            		= e(Input::get('name'));
            $item->item_type        = e(Input::get('item_type'));
          
            $item->user_id          	= Sentry::getId();

            // Was the asset created?
            if($item->save()) {
                // Redirect to the new item  page
                return Redirect::to("admin/settings/items")->with('success', Lang::get('admin/items/message.create.success'));
            }
        }

        // Redirect to the item create page
        return Redirect::to('admin/settings/items/create')->with('error', Lang::get('admin/items/message.create.error'));


    }

    /**
     * Item update.
     *
     * @param  int  $itemId
     * @return View
     */
    public function getEdit($itemId = null)
    {
        // Check if the item exists
        if (is_null($item = Item::find($itemId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/items')->with('error', Lang::get('admin/items/message.does_not_exist'));
        }

        // Show the page
        //$item_options = array('' => 'Top Level') + Item::lists('name', 'id');

        $item_options = array('' => 'Top Level') + DB::table('items')->where('id', '!=', $itemId)->lists('name', 'id');
        $item_types= array('' => '', 'asset' => 'Asset', 'accessory' => 'Accessory', 'consumable' => 'Consumable');

        return View::make('backend/items/edit', compact('item'))
        ->with('item_options',$item_options)
        ->with('item_types',$item_types);
    }


    /**
     * Item update form processing page.
     *
     * @param  int  $itemId
     * @return Redirect
     */
    public function postEdit($itemId = null)
    {
        // Check if the blog post exists
        if (is_null($item = Item::find($itemId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/items')->with('error', Lang::get('admin/items/message.does_not_exist'));
        }


        // get the POST data
        $new = Input::all();

        // attempt validation
        $validator = Validator::make(Input::all(), $item->validationRules($itemId));


        if ($validator->fails())
        {
            // The given data did not pass validation
            return Redirect::back()->withInput()->withErrors($validator->messages());
        }
        // attempt validation
        else {

            // Update the item data
            $item->name            = e(Input::get('name'));
            $item->item_type        = e(Input::get('item_type'));
          

            // Was the asset created?
            if($item->save()) {
                // Redirect to the new item page
                return Redirect::to("admin/settings/items")->with('success', Lang::get('admin/items/message.update.success'));
            }
        }

        // Redirect to the item management page
        return Redirect::to("admin/settings/items/$itemID/edit")->with('error', Lang::get('admin/items/message.update.error'));

    }

    /**
     * Delete the given item.
     *
     * @param  int  $itemId
     * @return Redirect
     */
    public function getDelete($itemId)
    {
        // Check if the item exists
        if (is_null($item = Item::find($itemId))) {
            // Redirect to the blogs management page
            return Redirect::to('admin/settings/items')->with('error', Lang::get('admin/items/message.not_found'));
        }


        if ($item->has_models() > 0) {

            // Redirect to the asset management page
            return Redirect::to('admin/settings/items')->with('error', Lang::get('admin/items/message.assoc_users'));
        } else {

            $item->delete();

            // Redirect to the locations management page
            return Redirect::to('admin/settings/items')->with('success', Lang::get('admin/items/message.delete.success'));
        }


    }



    /**
    *  Get the asset information to present to the item view page
    *
    * @param  int  $assetId
    * @return View
    **/
    public function getView($itemID = null)
    {
        $item = Item::find($itemID);

        if (isset($item->id)) {
                return View::make('backend/items/view', compact('item'));
        } else {
            // Prepare the error message
            $error = Lang::get('admin/items/message.does_not_exist', compact('id'));

            // Redirect to the user management page
            return Redirect::route('items')->with('error', $error);
        }


    }

    public function getDatatable()
    {
        // Grab all the items
        $items = Item::orderBy('created_at', 'DESC')->get();
        $actions = new \Chumper\Datatable\Columns\FunctionColumn('actions', function($items) {
            return '<a href="'.route('update/item', $items->id).'" class="btn btn-warning btn-sm" style="margin-right:5px;"><i class="fa fa-pencil icon-white"></i></a><a data-html="false" class="btn delete-asset btn-danger btn-sm" data-toggle="modal" href="'.route('delete/item', $items->id).'" data-content="'.Lang::get('admin/items/message.delete.confirm').'" data-title="'.Lang::get('general.delete').' '.htmlspecialchars($items->name).'?" onClick="return false;"><i class="fa fa-trash icon-white"></i></a>';
        });

        return Datatable::collection($items)
        ->showColumns('name')
        ->addColumn('item_type', function($items) {
            return ucwords($items->item_type);
        })
       
        ->addColumn($actions)
        ->searchColumns('name','item_type','actions')
        ->orderColumns('name','item_type','actions')
        ->make();
    }

    public function getDataView($itemID) {
        $item = Item::find($itemID);
        $itemassets = $item->assets;

        $actions = new \Chumper\Datatable\Columns\FunctionColumn('actions', function ($itemassets)
            {
                if (($itemassets->assigned_to !='') && ($itemassets->assigned_to > 0)) {
                    return '<a href="'.route('checkin/hardware', $itemassets->id).'" class="btn btn-primary btn-sm">'.Lang::get('general.checkin').'</a>';
                } else {
                    return '<a href="'.route('checkout/hardware', $itemassets->id).'" class="btn btn-info btn-sm">'.Lang::get('general.checkout').'</a>';
                }
            });

        return Datatable::collection($itemassets)
        ->addColumn('name', function ($itemassets) {
            return link_to('/hardware/'.$itemassets->id.'/view', $itemassets->name);
        })
        ->addColumn('asset_tag', function ($itemassets) {
            return link_to('/hardware/'.$itemassets->id.'/view', $itemassets->asset_tag);
        })
        ->addColumn('assigned_to', function ($itemassets) {
            if ($itemassets->assigned_to) {
                return link_to('/admin/users/'.$itemassets->assigned_to.'/view', $itemassets->assigneduser->fullName());
            }
        })
        ->addColumn($actions)
        ->searchColumns('name','asset_tag','assigned_to','actions')
        ->orderColumns('name','asset_tag','assigned_to','actions')
        ->make();
    }


}
