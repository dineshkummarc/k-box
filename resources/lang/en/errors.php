<?php

return [

	/*
	|--------------------------------------------------------------------------
	| Generic Errors Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines are used inside for rendering error messages
	|
	*/

	'unknown' => 'Unknown generic error in the request',

	'upload' => array(
		'simple' => 'Upload error :description',
		'filenamepolicy' => 'The file :filename is not respecting the naming convetion.',
		'filealreadyexists' => 'The file :filename already exists.',
	),

	'group_already_exists_exception' => array(
		'only_name' => 'A collection named ":name" already exists.',
		'name_and_parent' => 'The collection ":name" under ":parent" already exists.',
	),
    
    'generic_text' => 'Oops! something unexpected has happened.',
    'generic_text_alt' => 'Oops! something unexpected has happened. :error',
    'generic_title' => 'Oops!',


	'reindex_all' => 'The reindex all procedure cannot be completed due to an error. See the logs or contact the administrator.',

	'token_mismatch_exception' => 'Seems that your session is somehow expired, please refresh the page and then continue with your work. Thanks.',

	'not_found' => 'The resource you are looking for cannot be found.',
	
	'document_not_found' => 'The document you are looking for cannot be found or was deleted.',

	'forbidden_exception' => 'You don\'t have access to the page.',
	'forbidden_edit_document_exception' => 'You cannot edit the document.',
	'forbidden_see_document_exception' => 'You cannot view the document as it is personal of a user.',
	
	'fatal' => 'Fatal error :reason',

	'panels' => array(
		'title' => 'Oops! something unexpected has happened.',
		'prevent_edit' => 'You cannot edit :name',
	),

	'import' => array(
		'folder_not_readable' => 'The folder :folder is not readable. Make sure you have read permission.',
		'url_already_exists' => 'A file from the same website url (:url) has already been imported.',
		'download_error' => 'The document ":url" cannot be downloaded. :error',
	),


	'group_edit_institution' => "You cannot edit institution level groups.",
	'group_edit_else' => "You cannot edit someone else's groups.",

	'503_title' => 'K-Link DMS Maintenance',
	'503_text' => 'The <strong>DMS</strong> is currently in<br/><strong>maintenance</strong><br/><small>will be back shortly :)</small>',

	'500_title' => 'Error - K-Link DMS',
	'500_text' => 'Oh Snap! Something <strong>bad</strong><br/>and unexpected <strong>happened</strong>,<br/>we are deeply sorry.',

	'404_title' => 'Not Found on the K-Link DMS',
	'404_text' => 'Woops! Looks like <strong>the page</strong><br/>your are looking for<br/><strong>doesn\'t exist</strong> anymore.',
	
	'401_title' => 'You cannot view the page K-Link DMS',
	'401_text' => 'Woops! Looks like you <strong>cannot</strong> view the page<br/>due to your <strong>Authorization</strong> level.',
    
    '403_title' => 'You don\'t have the permission to view the page',
	'403_text' => 'Woops! Looks like you <strong>cannot</strong> view the page<br/>due to your <strong>Authorization</strong> level.',

	'405_title' => 'Method Not Allowed on the K-Link DMS',
	'405_text' => 'Don\'t call me like this again.',
	
	'413_title' => 'Document Excessive File Size',
	'413_text' => 'The file your are trying to upload exceeds the maximum allowed file size.',
	
	'klink_exception_title' => 'K-Link Services Error',
	'klink_exception_text' => 'There were a problem connecting to the K-Link Services.',
    
    'reindex_failed' => 'Search might not be up-to-date with latest changes, please consult the support team for more information.',
    
    'page_loading_title' => 'Loading Problem', 
    'page_loading_text' => 'Page loading seems slow and some functionality may not be available, please refresh the page.',
    
    'dragdrop' => [
        'not_permitted_title' => 'Drag and Drop not Available',
        'not_permitted_text' => 'You cannot perform tha drag and drop operation.',
        'link_not_permitted_title' => 'Drag links is not available',
        'link_not_permitted_text' => 'Currently you cannot drag and drop links to websites.',
    ],


    'support_widget_opened_for_you' => 'We have opened the support widget for you, please drop us a line so we can investigate on the error. Thanks for your support.',
    'go_back_btn' => 'I understand. Take me out of here.',
	
	'preference_not_saved_title' => 'Preference not saved',
	'preference_not_saved_text' => 'Sorry, we were unable to save your preference, please try again later.',

];
