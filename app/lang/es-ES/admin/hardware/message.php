<?php

return array(

    'undeployable' 		=> '<strong>Atención: </strong> Este equipo está marcado como no isntalabre.
                        Si no es correcto, actualiza su estado.',
    'does_not_exist' 	=> 'Equipo inexistente.',
    'does_not_exist_or_not_requestable' => 'Nice try. That asset does not exist or is not requestable.',
    'assoc_users'	 	=> 'Equipo asignado a un usuario, no se puede eliminar.',

    'create' => array(
        'error'   		=> 'Equipo no creado, intentalo de nuevo. :(',
        'success' 		=> 'Equipo creado. :)'
    ),

    'update' => array(
        'error'   			=> 'Equipo no actualizado, intentalo de nuevo',
        'success' 			=> 'Equipo actualizado.',
        'nothing_updated'	=>  'Ningún campo fue seleccionado, por lo que nada ha sido actualizado.',
    ),

    'restore' => array(
        'error'   		=> 'El equipo no fue restaurado, por favor intente nuevamente',
        'success' 		=> 'Equipo restaurado correctamente.'
    ),

    'deletefile' => array(
        'error'   => 'Archivo no eliminado. Por favor, vuelva a intentarlo.',
        'success' => 'Archivo eliminado correctamente.',
    ),

    'upload' => array(
        'error'   => 'Archivo(s) no cargado. Por favor, vuelva a intentarlo.',
        'success' => 'Archivo(s) cargado correctamente.',
        'nofiles' => 'You did not select any files for upload, or the file you are trying to upload is too large',
        'invalidfiles' => 'Uno o más sus archivos es demasiado grande o es de un tipo no permitido. Los tipos de archivo permitidos son png, gif, jpg, doc, docx, pdf y txt.',
    ),


    'delete' => array(
        'confirm'   	=> 'Estás seguro que quieres eliminar el equipo?',
        'error'   		=> 'Equipo no eliminado, intentalo de nuevo.',
        'success' 		=> 'Equipo eliminado.'
    ),

    'checkout' => array(
        'error'   		=> 'Equipo no asignado, intentalo de nuevo',
        'success' 		=> 'Equipo asignado.',
        'user_does_not_exist' => 'Este usuario no es correcto. Intentalo de nuevo.'
    ),

    'checkin' => array(
        'error'   		=> 'No se ha quitado el equipo. Intentalo de nuevo.',
        'success' 		=> 'Equipo quitado correctamente.',
        'user_does_not_exist' => 'Este usuario no es correcto. Intentalo de nuevo.'
    )

);
