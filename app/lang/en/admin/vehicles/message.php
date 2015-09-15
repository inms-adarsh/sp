<?php

return array(

    'does_not_exist' => 'Vehicle does not exist.',
    'assoc_users'	 => 'This vehicle is currently associated with one or more assets and cannot be deleted. Please delete the assets, and then try deleting again. ',


    'create' => array(
        'error'   => 'Vehicle was not created, please try again.',
        'success' => 'Vehicle created successfully.',
        'duplicate_set' => 'An asset vehicle with that name, manufacturer and vehicle number already exists.',
    ),

    'update' => array(
        'error'   => 'Vehicle was not updated, please try again',
        'success' => 'Vehicle updated successfully.'
    ),

    'delete' => array(
        'confirm'   => 'Are you sure you wish to delete this asset vehicle?',
        'error'   => 'There was an issue deleting the vehicle. Please try again.',
        'success' => 'The vehicle was deleted successfully.'
    ),

    'restore' => array(
        'error'   		=> 'Vehicle was not restored, please try again',
        'success' 		=> 'Vehicle restored successfully.'
    ),

);
