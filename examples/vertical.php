<?php
#------------------------------------------------
#	EXAMPLE 3: Vertical layout
#------------------------------------------------
# In this file we...
# Do exactly like we did in Example 1, 
# except, we'll play around with the 'vertical' display and help blocks as well as disabling labels.



if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}

use afflicto\form\form as form;



$form = new form('vertical', 'POST');
$form->autocomplete(false);


# Help blocks are <small>descriptions</small> about a field.
# Let's add one to the username:
$form->username = form::text('Username', 'username')->autofocus();
$form->username->help('Letters & numbers only.');

$form->password = form::password('Password', 'password');

$form->submit = form::submit('Sign up', 'signup');

$form->reset = form::reset('Reset', 'reset');


#------------------------------------------------
#	Now that we have created our form
#	We can ask if anything was posted this time
#------------------------------------------------
if ($form->wasPosted()) {

	# if it was, we'll export all the values the key->value pair
	$array = $form->export();

	# this array looks like:
	# array(
	#	'username' => 'foobar',
	#	'email' => 'foo@bar.xyz'
	#)

	# now it's up to you to determine what to do with the data, and possible output a message
	# You can also output the form by echoing $form->display()
	echo 'Thanks you for signing up. <a href="#">Log in</a>.';
	echo 'here are the values posted:<br>';
	var_dump($array);
}else {

	# if nothing was posted, we'll display the form
	echo $form->display('vertical');

	# we're currently using the 'default' render engine. It has no special markup but has has two arguments
	# the first is whether to render labels and second whether to render help blocks. We haven't covered help blocks yet, so check the next example!
}

