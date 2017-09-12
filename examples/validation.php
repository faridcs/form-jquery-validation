<?php
#------------------------------------------------
#	EXAMPLE 5: Validation
#------------------------------------------------
# In this example we'll look at server-side validation
# by adding rules to our form elements.
# For a list of supported validation rules, refer to the documentation
# It supports min, max, minlength, maxlength, alpha, alphanumeric, url and email.
# More validators like phone numbers, zip codes, visa cards will be implemented shortly, so stay tuned!


if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

use afflicto\form\form as form;



$form = new form('validation', 'POST');
$form->autocomplete(false);

$form->username = form::text('Username', 'username')->autofocus();


# let's add some rules to the username, it is required and must be alphanumeric (letters & numbers only)
# as well as be between 3 and 10 characters long
$form->username->rules(array(
	'required' => true,		# makes the field required
	'minlen' => 3,
	'maxlen' => 6,
));



# as an example, let's add an email field using the HTML5 input type 'email'.
# You can use a text input as well
# This one will not be requried, but if there is a value, it must be a valid email address
$form->email = form::text('Email', 'email');
$form->email->rules(array(
	'email' => true
));

$form->username->help('Letters & numbers only.');

$form->password = form::password('Password', 'password');

$form->passwordAgain = form::password('Confirm Password', 'passwordagain')->rules(array('equalTo' => 'password', 'required' => true));

$form->submit = form::submit('Sign up', 'signup');

$form->reset = form::reset('Reset', 'reset');


if ($form->wasPosted()) {

	# run validation
	if ($form->valid()) {

		# it's valid. we do our processing (talk to a database, send emails and what not...)

		echo 'server-side validation succeeded!<br>';

	}else {
		# this time around, elements will have errors attached to them
		echo $form->display('table');
	}

}else {

	# if nothing was posted, we'll display the form
	echo $form->display('table');

	# we're currently using the 'default' render engine. It has no special markup but has has two arguments
	# the first is whether to render labels and second whether to render help blocks. We haven't covered help blocks yet, so check the next example!
}