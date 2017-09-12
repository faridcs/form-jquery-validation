<?php
#------------------------------------------------
#	EXAMPLE 11: Integration
#------------------------------------------------
# This is part 2 of the integration example.
# Please make sure you read examples/integration.php.

if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

$form = include('forms/login.php');

if ($form->wasPosted()) {

	# check if the fields are valid
	if ($form->valid()) {

		echo 'You are now logged in.';

	}else {
		# Validation failed, let's tell the user that and possibly give a link back to the form?
		# Or we can display the form here.
		# Make sure it goes to integration_process.php again.
		# (you could set that in forms/login.php though)

		$form->attr('action', 'integration_process.php');
		echo $form->display('table');
		echo 'Validation failed';
	}

}else {
	# If one tried to access this page without sending any data, just redirect to the form.
	header('location: integration.php');
}