<?php
#------------------------------------------------
#	EXAMPLE 1: Basic Usage
#------------------------------------------------
# In this file we...
# 1. require_once all the form and form element classes
# 2. import/alias/use the afflicto\form\form class.
# 3. instantiate a new form with name=myform and method=POST
# 4. turn off autocomplete on the form
# 5. add 6 form elements, including a submit and reset button.
# 6. set all form values equal to what was posted (if anything was posted)
# 7. determine if anything was POSTed or not (if the user submitted any data)
#		In which case, we process the data (you'll probably send an email or talk to a database at this point)
#		Then we echo a simple message.
# 8. Otherwise, simply print out the form. Next time around, the user probably clicked submit and the previous steps repeat and we go through step 7.



#------------------------------------------------
#	First, require the form class
#------------------------------------------------
if (!class_exists('\afflicto\form\form')) {
	require_once '../src/afflicto/form/form.php';
	require_once '../src/afflicto/form/validators.php';
}


#------------------------------------------------
#	Alias the form class for easier use.
#------------------------------------------------
use afflicto\form\form as form;


# First, we create a new form. It takes three aguments: name, method and action.
# action defaults to '', which will POST to the current URL, which we'll do.
$form = new form('basic', 'POST');

# turn off autocomplete
$form->autocomplete(false);
#------------------------------------------------
#	The form and all form elements extend a base
#	html class found in src/afflicto/form/html/element.php
#	They all support the following methods:
#	->addClass('class anotherclass thirdone')
#	->removeClass('anotherclass')
#	->attr('attribute', 'value')
#	->attr('name') returns the attribute value
#	Similar to jQuery. The 'setter' methods are chainable too.
#------------------------------------------------


#--------------------------------------------------------------------------------------------------
#	here's how we add form elements, the property names for the form elements on the $form object are completely arbitrary.
#	methods that don't return a value return the $this instance, for chainability.
#	Let's create a text field. The first argument is the label we'll use for it. (you don't need to create labels, rendering engines do that for you)
#	The second is the name attribute.
#--------------------------------------------------------------------------------------------------
$form->username = form::text('Username', 'username')->autofocus(); # <-- chaining (returns $this)

# password field
$form->password = form::password('Password', 'password');

# email field
$form->email = form::email('Email', 'email');

# Checkbox
$form->newsletter = form::checkbox('Signup for Newsletter', 'newsletter');

# submit button
$form->submit = form::submit('Sign up', 'signup');

# reset button
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
	echo $form->display();
	
	# we're currently using the 'default' render engine. It has no special markup but has has two arguments
	# the first is whether to render labels and second whether to render help blocks. We haven't covered help blocks yet, so check the next example!
}