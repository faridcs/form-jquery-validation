<?php
#------------------------------------------------
#	EXAMPLE 14: CSRF (Cross-site request forgery protection)
#------------------------------------------------
# In this file we learn how to enable CSRF protection on a form.
# The form is the same as the one in the first example (basic.php) except that this time, we're protecting against CSRF attacks.
#
# NOTE: the lines specific to CSRF are lines 21, 36 and 50


if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}
use afflicto\form\form as form;


$form = new form('basic', 'POST');


$form->useCSRF(true);


$form->autocomplete(false);
$form->username = form::text('Username', 'username')->autofocus();
$form->password = form::password('Password', 'password');
$form->email = form::email('Email', 'email');
$form->newsletter = form::checkbox('Signup for Newsletter', 'newsletter');
$form->submit = form::submit('Sign up', 'signup');
$form->reset = form::reset('Reset', 'reset');



if ($form->wasPosted()) {

	if ($form->validCSRF()) {
		
		$array = $form->export();

		echo 'Success, CSRF token accepted!<br>';
		var_dump($array);

	}else {
		die('Oops! Invalid CSRF Token received!');
	}

}else {
	
	# Make sure that when you run the display() function, a new CSRF Token is generated. So do not run the display() method before running the validCSRF() method.
	echo $form->display();
}