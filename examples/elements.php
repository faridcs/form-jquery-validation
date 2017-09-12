<?php
#------------------------------------------------
#	EXAMPLE 2: Form Elements
#------------------------------------------------
# This example makes use of all possible form elements
# text, textarea, password, email, url, select, checkbox etc.
# We'll also make use of the table layout since it's a bit easier to see.
# Refer to the table.php example file for more on the table layout.


if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}


use afflicto\form\form as form;



$form = new form('elements', 'POST');

# turn off autocomplete
$form->autocomplete(false);

$form->firstname = form::text('First name', 'firstname')->autofocus();

$form->lastname = form::text('Last name', 'lastname');

$form->gender = form::select('Gender', 'gender', array('male' => 'Male', 'female' => 'Female'), 'female');

$form->email = form::email('E-mail', 'email');

$form->password = form::password('Password', 'password');

$form->human = form::checkbox('Are you human?', 'human');

# Radio buttons look a bit strange if you create them like any other field.
# Each radio element is related in some way.
$form->drink = form::radio('Favorite drink', 'favoritedrink', 'cola');
$form->cola = form::radio('Coca Cola', 'favoritedrink', 'cola');
$form->beer = form::radio('Beer', 'favoritedrink', 'beer');

# So let's use the radioList custom element
# We give it the label and name (like any other element) then an associative array and optionally a checked value (just like the select element)
$form->computer = form::radioList('Operating System', 'operatingsystem', array('win' => 'Windows', 'mac' => 'Mac OSX', 'linux' => 'Linux'), 'mac');

$form->submit = form::submit('Submit', 'submit');


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
	$content = $form->display('table');
}else {
	
	# if nothing was posted, we'll display the form
	$content = $form->display('table');
	
	# we're currently using the 'default' render engine. It has no special markup but has has two arguments
	# the first is whether to render labels and second whether to render help blocks. We haven't covered help blocks yet, so check the next example!
}?><!DOCTYPE html>
<html>
<head></head>
<body>
	<div style="margin: 20px auto; max-width: 600px;">
		<?php echo $content; ?>
	</div>
</body>
</html>