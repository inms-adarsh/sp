<?php

class Vehicle extends Elegant
{
    use SoftDeletingTrait;
    protected $dates = ['deleted_at'];
    protected $table = 'vehicles';

    // Declare the rules for the form validation
    protected $rules = array(
        'vehicleno'   		=> 'required|alpha_space|min:2|max:255|unique:vehicles,deleted_at,NULL',
        'category_id'   	=> 'required|integer',
       
        'user_id' => 'integer',
    );
     public function category()
    {
        return $this->belongsTo('Category', 'category_id');
    }
   /* public function assets()
    {
        return $this->hasMany('Asset', 'vehicle_id');
    }

   

    public function depreciation()
    {
        return $this->belongsTo('Depreciation','depreciation_id');
    }

    public function adminuser()
    {
        return $this->belongsTo('User','user_id');
    }

    public function manufacturer()
    {
        return $this->belongsTo('Manufacturer','manufacturer_id');
    }*/

    /**
	* -----------------------------------------------
	* BEGIN QUERY SCOPES
	* -----------------------------------------------
	**/

    /**
	* Query builder scope for Deleted assets
	*
	* @param  Illuminate\Database\Query\Builder  $query  Query builder instance
	* @return Illuminate\Database\Query\Builder          Modified query builder
	*/

	public function scopeDeleted($query)
	{
		return $query->whereNotNull('deleted_at');
	}

    /**
     * scopeInCategory
     * Get all vehicles that are in the array of category ids
     *
     * @param       $query
     * @param array $categoryIdListing
     *
     * @return mixed
     * @author  Vincent Sposato <vincent.sposato@gmail.com>
     * @version v1.0
     */
    public function scopeInCategory( $query, array $categoryIdListing )
    {

        return $query->whereIn( 'category_id', $categoryIdListing );
    }

}
